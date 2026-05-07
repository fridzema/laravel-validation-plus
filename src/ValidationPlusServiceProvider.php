<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\HttpFoundation\Response;

final class ValidationPlusServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('validation-plus')
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(WarningBag::class, function (): WarningBag {
            $bag = new WarningBag;

            $request = $this->app->make('request');
            if ($request->hasSession()) {
                /** @var string $sessionKey */
                $sessionKey = config('validation-plus.session_key', 'warnings');
                $flashed = $request->session()->get($sessionKey);
                if ($flashed instanceof WarningBag) {
                    $bag->merge($flashed->getMessages());
                    $bag->markResolvedFromSession();
                }
            }

            return $bag;
        });

        $this->app->singleton(WarningValidator::class, fn (): WarningValidator => new WarningValidator);
    }

    public function packageBooted(): void
    {
        View::composer('*', function (\Illuminate\View\View $view): void {
            $view->with('warnings', app(WarningBag::class));
        });

        /** @var Router $router */
        $router = $this->app->make('router');
        $router->aliasMiddleware('warnings', Middleware\ShareWarnings::class);

        $this->registerBladeDirectives();
        $this->registerTestingMacros();
    }

    private function registerBladeDirectives(): void
    {
        $class = addslashes(WarningBag::class);

        Blade::directive('warning', function (string $expression) use ($class): string {
            return "<?php foreach (app('{$class}')->get({$expression}) as \$message): ?>";
        });

        Blade::directive('endwarning', fn (): string => '<?php endforeach; ?>');
    }

    private function registerTestingMacros(): void
    {
        if (! class_exists(TestResponse::class)) { // @codeCoverageIgnoreStart
            return;
        } // @codeCoverageIgnoreEnd

        TestResponse::macro('assertHasWarning', function (string $key, ?string $message = null): TestResponse {
            /** @var TestResponse<Response> $this */
            /** @var array<string, list<string>> $warnings */
            $warnings = $this->getWarningsFromResponse();

            Assert::assertTrue(
                isset($warnings[$key]),
                "Expected warning for key [{$key}] but none was found."
            );

            if ($message !== null) {
                Assert::assertContains(
                    $message,
                    $warnings[$key],
                    "Expected warning message [{$message}] for key [{$key}] was not found."
                );
            }

            return $this;
        });

        TestResponse::macro('assertHasNoWarnings', function (?string $key = null): TestResponse {
            /** @var TestResponse<Response> $this */
            /** @var array<string, list<string>> $warnings */
            $warnings = $this->getWarningsFromResponse();

            if ($key !== null) {
                Assert::assertFalse(
                    isset($warnings[$key]),
                    "Unexpected warning found for key [{$key}]."
                );
            } else {
                Assert::assertEmpty(
                    $warnings,
                    'Expected no warnings but found: '.json_encode($warnings)
                );
            }

            return $this;
        });

        /** @param array<string, list<string>> $expected */
        TestResponse::macro('assertWarnings', function (array $expected): TestResponse {
            /** @var TestResponse<Response> $this */
            $actual = $this->getWarningsFromResponse();

            Assert::assertEquals(
                $expected,
                $actual,
                'Response warnings do not match expected. Got: '.json_encode($actual)
            );

            return $this;
        });

        /** @return array<string, list<string>> */
        TestResponse::macro('getWarningsFromResponse', function (): array {
            /** @var TestResponse<Response> $this */
            // 1. JSON body (most complete when inject_json=true)
            if ($this->headers->has('Content-Type') && str_contains((string) $this->headers->get('Content-Type'), 'json')) {
                /** @var string $jsonKey */
                $jsonKey = config('validation-plus.json_key', 'warnings');
                $data = $this->json();
                if (is_array($data) && isset($data[$jsonKey])) {
                    /** @var array<string, list<string>> $fieldWarnings */
                    $fieldWarnings = $data[$jsonKey];

                    return $fieldWarnings;
                }
            }

            // 2. Warning data header (covers inject_json=false, 204s, scalar bodies)
            /** @var string $headerName */
            $headerName = config('validation-plus.header', 'X-Validation-Warnings');
            $headerData = $this->headers->get($headerName.'-Data');
            if ($headerData !== null) {
                $decoded = json_decode($headerData, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }

            // 3. Session (web responses)
            /** @var string $sessionKey */
            $sessionKey = config('validation-plus.session_key', 'warnings');
            $session = app('session.store');
            if ($session->isStarted() && $session->has($sessionKey)) {
                $bag = $session->get($sessionKey);

                return $bag instanceof WarningBag ? $bag->getMessages() : [];
            }

            return [];
        });
    }
}
