<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\Middleware\ShareWarnings;
use Fridzema\ValidationPlus\WarningBag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

it('passes through when no warnings exist', function (): void {
    $request = Request::create('/test', 'GET');
    $middleware = new ShareWarnings;

    $response = $middleware->handle($request, fn () => new Response('OK'));

    expect($response->getStatusCode())->toBe(200);
});

it('adds header to json response when warnings exist', function (): void {
    app(WarningBag::class)->merge(['email' => ['Warning about email.']]);

    $request = Request::create('/test', 'GET');
    $request->headers->set('Accept', 'application/json');

    $middleware = new ShareWarnings;

    $response = $middleware->handle(
        $request,
        fn () => new JsonResponse(['data' => 'test']),
    );

    expect($response->headers->get(config('validation-plus.header')))->toBe('true');
});

it('injects warnings into json response body', function (): void {
    app(WarningBag::class)->merge(['email' => ['Warning about email.']]);

    $request = Request::create('/test', 'GET');
    $request->headers->set('Accept', 'application/json');

    $middleware = new ShareWarnings;

    $response = $middleware->handle(
        $request,
        fn () => new JsonResponse(['data' => 'test']),
    );

    $data = $response->getData(assoc: true);

    expect($data)->toHaveKey('warnings');
    expect($data['warnings']['email'])->toContain('Warning about email.');
});

it('does not inject json when inject_json config is false', function (): void {
    config()->set('validation-plus.inject_json', false);

    app(WarningBag::class)->merge(['email' => ['Warning.']]);

    $request = Request::create('/test', 'GET');
    $request->headers->set('Accept', 'application/json');

    $middleware = new ShareWarnings;

    $response = $middleware->handle(
        $request,
        fn () => new JsonResponse(['data' => 'test']),
    );

    $data = $response->getData(assoc: true);

    // Header should still be present
    expect($response->headers->get(config('validation-plus.header')))->toBe('true');
    // But no warnings injected into body
    expect($data)->not->toHaveKey('warnings');
});

it('flashes warnings to session for web requests', function (): void {
    app(WarningBag::class)->merge(['name' => ['Name warning.']]);

    $request = Request::create('/test', 'GET');
    $request->setLaravelSession(app('session.store'));

    $middleware = new ShareWarnings;

    $middleware->handle($request, fn () => new Response('OK'));

    $sessionKey = config('validation-plus.session_key', 'warnings');
    $flashed = $request->session()->get($sessionKey);

    expect($flashed)->toBeInstanceOf(WarningBag::class);
    expect($flashed->has('name'))->toBeTrue();
});

it('does not modify response when warning bag is empty', function (): void {
    $request = Request::create('/test', 'GET');
    $request->headers->set('Accept', 'application/json');

    $middleware = new ShareWarnings;

    $response = $middleware->handle(
        $request,
        fn () => new JsonResponse(['data' => 'test']),
    );

    expect($response->headers->has(config('validation-plus.header')))->toBeFalse();

    $data = $response->getData(assoc: true);
    expect($data)->not->toHaveKey('warnings');
});
