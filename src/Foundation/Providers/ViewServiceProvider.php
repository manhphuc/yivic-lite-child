<?php

declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Foundation\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\FileEngine;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;
use RuntimeException;
use Yivic\YivicLiteChild\Theme\ThemeContext;
use Yivic\YivicLiteChild\Theme\ThemeContextFactory;

/**
 * ViewServiceProvider (Illuminate\View / Blade).
 *
 * Responsibilities:
 * - Register view-related services into the theme container (Laravel-style).
 * - Bind ThemeContext as the SINGLE source of truth for:
 *   - view paths (child-first, parent fallback)
 *   - compiled directory (and directory creation policy)
 *
 * Design rules:
 * - register(): bind services only (no heavy work).
 * - boot(): share globals / post-registration configuration.
 *
 * Anti-duplication policy:
 * - DO NOT normalize view paths or create compiled directories here.
 * - Those concerns live in ThemeContextFactory + ThemeContext only.
 */
final class ViewServiceProvider extends ServiceProvider {
    public function register(): void {
        // Core dependencies (lazy, override-friendly).
        $this->container()->singleton( Filesystem::class, static fn (): Filesystem => new Filesystem() );
        $this->container()->singleton( Dispatcher::class, fn (): Dispatcher => new Dispatcher( $this->app->container() ) );

        // Aliases (match container alias signature: alias($abstract, $alias)).
        $this->container()->alias( Filesystem::class, 'files' );
        $this->container()->alias( Dispatcher::class, 'events' );

        // ThemeContext: build once per request (single source of truth).
        $this->container()->singleton(
            ThemeContext::class,
            fn (): ThemeContext => ( new ThemeContextFactory() )->make( $this->app )
        );
        $this->container()->alias( ThemeContext::class, 'theme' );

        // View factory (lazy).
        $this->container()->singleton( ViewFactory::class, function (): ViewFactory {
            $files  = $this->container()->make( Filesystem::class );
            $events = $this->container()->make( Dispatcher::class );

            return $this->buildViewFactory( $files, $events );
        } );

        // Laravel-like aliases.
        $this->container()->alias( ViewFactory::class, 'view' );

        // Bind the contract as well (future-proof).
        $this->container()->singleton(
            ViewFactoryContract::class,
            fn (): ViewFactoryContract => $this->container()->make( ViewFactory::class )
        );
    }

    public function boot(): void {
        $view = $this->resolveViewFactory();

        // Share commonly-used globals.
        $view->share( 'app', $this->app );
        $view->share( 'config', $this->config()->all() );

        // Share ThemeContext across all views as $theme.
        $view->share( 'theme', $this->resolveThemeContext() );
    }

    /**
     * Build the Illuminate ViewFactory using ThemeContext as the single source of truth.
     */
    private function buildViewFactory( Filesystem $files, Dispatcher $events ): ViewFactory {
        $theme = $this->resolveThemeContext();

        // Ensure compiled directory exists (policy lives in ThemeContext).
        $theme->ensureCompiledDirExists();

        $resolver = new EngineResolver();

        // Blade compiler.
        $bladeCompiler = new BladeCompiler($files, $theme->compiledViewPath());

        // Engines: blade + plain PHP.
        $resolver->register( 'blade', static fn () => new CompilerEngine( $bladeCompiler ) );
        $resolver->register( 'php', static fn () => new FileEngine( $files ) );

        /**
         * Deterministic view resolution:
         * - "home" resolves to "home.blade.php" or "home.php"
         * - Search order is defined by ThemeContext (child first, then parent).
         */
        $finder = new FileViewFinder( $files, $theme->viewPaths(), [ 'blade.php', 'php' ] );

        return new ViewFactory( $resolver, $finder, $events );
    }

    /**
     * Resolve the ViewFactory instance safely.
     *
     * Why this method exists:
     * - Keeps exception handling centralized and consistent.
     * - Makes IDE/static analyzers happy (no unhandled BindingResolutionException).
     * - Provides a single place to improve error reporting in the future.
     *
     * @throws RuntimeException When the view factory cannot be resolved.
     */
    private function resolveViewFactory(): ViewFactory {
        try {
            return $this->container()->make( ViewFactory::class );
        } catch ( BindingResolutionException $e ) {
            // Do not leak container internals or file paths; keep message high-level.
            throw new RuntimeException( 'View system is not available: failed to resolve ViewFactory binding.', 0, $e );
        }
    }

    /**
     * Resolve ThemeContext safely.
     *
     * @throws RuntimeException When ThemeContext cannot be resolved.
     */
    private function resolveThemeContext(): ThemeContext {
        try {
            return $this->container()->make( ThemeContext::class );
        } catch ( BindingResolutionException $e ) {
            throw new RuntimeException( 'ThemeContext is not available: failed to resolve ThemeContext binding.', 0, $e );
        }
    }
}
