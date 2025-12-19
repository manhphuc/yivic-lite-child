<?php
declare( strict_types = 1 );

use Illuminate\Contracts\Container\BindingResolutionException;
use Yivic\YivicLiteChild\Foundation\Application;

if ( ! function_exists('app' ) ) {
    /**
     * Laravel-like app() helper (theme-scoped).
     *
     * This helper is intentionally minimal and side-effect free.
     *
     * Responsibilities:
     * - Return the bootstrapped Theme Application instance when called with no args.
     * - Resolve services from the underlying DI container when an abstract is provided.
     *
     * Why theme-scoped?
     * - Your plugin already defines `view()`.
     * - The theme must avoid global helper collisions to remain composable.
     *
     * @template T
     * @param  class-string<T>|string|null $abstract Container binding / class name.
     * @return ($abstract is null ? Application : T|mixed)
     *
     * @throws RuntimeException When the theme app is not bootstrapped yet.
     * @throws RuntimeException When the container cannot resolve the service.
     */
    function app( ?string $abstract = null ): mixed {
        $application = $GLOBALS['yivic_theme_app'] ?? null;

        if ( ! $application instanceof Application ) {
            throw new RuntimeException( 'Theme Application is not bootstrapped yet.' );
        }

        if ( $abstract === null ) {
            return $application;
        }

        try {
            return $application->container()->make( $abstract );
        } catch ( BindingResolutionException $e ) {
            throw new RuntimeException(
                'Container cannot resolve [' . $abstract . ']. ' .
                'Ensure the service is bound (e.g. ViewServiceProvider::register()) before calling app().',
                0,
                $e
            );
        }
    }
}

if ( ! function_exists( 'theme_view' ) ) {
    /**
     * Theme-scoped Blade renderer.
     *
     * Returns rendered HTML as a string (no echo).
     * - This makes it safe for actions, filters, controllers, services, etc.
     * - The caller decides where/how to output.
     *
     * Uses the Illuminate view factory contract to remain decoupled from the
     * concrete implementation and enable future upgrades (custom engines, caching,
     * alternative factories).
     *
     * @param  string $name       Blade view name (e.g. "test", "pages.home").
     * @param  array  $data       View data.
     * @param  array  $mergeData  Additional merged data (same semantics as Laravel).
     * @return string             Rendered HTML.
     *
     * @throws RuntimeException   If the view factory is not bound/bootstrapped.
     */
    function theme_view( string $name, array $data = [], array $mergeData = [] ): string {
        /** @var \Illuminate\Contracts\View\Factory $factory */
        $factory = app( \Illuminate\Contracts\View\Factory::class );

        return $factory->make( $name, $data, $mergeData )->render();
    }
}
