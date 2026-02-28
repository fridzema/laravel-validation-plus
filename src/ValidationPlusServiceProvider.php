<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus;

use Illuminate\Support\Facades\View;
use Illuminate\Testing\TestResponse;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
        $this->app->singleton(WarningBag::class, fn (): WarningBag => new WarningBag);

        $this->app->bind(WarningValidator::class, fn (): WarningValidator => new WarningValidator);
    }

    public function packageBooted(): void
    {
        View::share('warnings', $this->app->make(WarningBag::class));

        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app->make('router');
        $router->aliasMiddleware('warnings', Middleware\ShareWarnings::class);

        $this->registerTestingMacros();
    }

    private function registerTestingMacros(): void
    {
        if (! class_exists(TestResponse::class)) {
            return;
        }

        TestResponse::macro('assertHasWarning', function (string $key, ?string $message = null): TestResponse {
            /** @var TestResponse $this */
            $warnings = $this->getWarningsFromResponse();

            \PHPUnit\Framework\Assert::assertTrue(
                isset($warnings[$key]),
                "Expected warning for key [{$key}] but none was found."
            );

            if ($message !== null) {
                \PHPUnit\Framework\Assert::assertContains(
                    $message,
                    $warnings[$key],
                    "Expected warning message [{$message}] for key [{$key}] was not found."
                );
            }

            return $this;
        });

        TestResponse::macro('assertHasNoWarnings', function (?string $key = null): TestResponse {
            /** @var TestResponse $this */
            $warnings = $this->getWarningsFromResponse();

            if ($key !== null) {
                \PHPUnit\Framework\Assert::assertFalse(
                    isset($warnings[$key]),
                    "Unexpected warning found for key [{$key}]."
                );
            } else {
                \PHPUnit\Framework\Assert::assertEmpty(
                    $warnings,
                    'Expected no warnings but found: '.json_encode($warnings)
                );
            }

            return $this;
        });

        TestResponse::macro('getWarningsFromResponse', function (): array {
            /** @var TestResponse $this */
            // Check JSON response first
            if ($this->headers->has('Content-Type') && str_contains((string) $this->headers->get('Content-Type'), 'json')) {
                $data = $this->json();

                return $data['warnings'] ?? [];
            }

            // Check session for web responses
            $session = $this->getSession();
            if ($session !== null && $session->has(config('validation-plus.session_key', 'warnings'))) {
                $bag = $session->get(config('validation-plus.session_key', 'warnings'));

                return $bag instanceof WarningBag ? $bag->getMessages() : [];
            }

            return [];
        });
    }
}
