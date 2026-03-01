<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus\Middleware;

use Closure;
use Fridzema\ValidationPlus\WarningBag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShareWarnings
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        /** @var WarningBag $warnings */
        $warnings = app(WarningBag::class);

        if ($warnings->isEmpty()) {
            return $response;
        }

        if ($request->hasSession()) {
            /** @var string $sessionKey */
            $sessionKey = config('validation-plus.session_key', 'warnings');
            $request->session()->flash($sessionKey, $warnings);
        }

        if ($request->expectsJson()) {
            /** @var string $headerName */
            $headerName = config('validation-plus.header', 'X-Validation-Warnings');
            $response->headers->set($headerName, 'true');

            /** @var array<string, list<string>> $messages */
            $messages = $warnings->getMessages();
            $response->headers->set($headerName.'-Data', (string) json_encode($messages));

            /** @var bool $injectJson */
            $injectJson = config('validation-plus.inject_json', true);

            if ($injectJson && $response instanceof JsonResponse) {
                $data = $response->getData(assoc: true);

                if (is_array($data)) {
                    $data['warnings'] = $messages;
                    $response->setData($data);
                }
            }
        }

        return $response;
    }
}
