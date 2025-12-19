<?php
declare(strict_types=1);

namespace Yivic\YivicLiteChild\Foundation\Providers;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\FileEngine;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;

/**
 * ViewServiceProvider (Illuminate\View / Blade).
 *
 * Responsibilities:
 * - Register view-related services into the theme container.
 * - Configure Blade compilation cache directory.
 * - Configure view discovery paths:
 *   - Child theme views first (override layer)
 *   - Parent theme views second (fallback layer)
 *
 * Config keys:
 * - view.paths    : array<string>  Directories to search for views.
 * - view.compiled : string         Directory for compiled Blade templates.
 *
 * Design goals:
 * - Lazy singletons for performance and override-friendliness.
 * - Theme-scoped bindings to avoid collisions with plugins.
 * - Deterministic view resolution (explicit extensions).
 */
final class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register bindings (no heavy work, no rendering).
     *
     * This method must be safe to call early in the bootstrap process.
     * All expensive work is deferred via singleton closures.
     */
    public function register(): void
    {
        $app = $this->app;

        // Core dependencies (lazy + override-friendly).
        $this->container()->singleton(Filesystem::class, fn () => new Filesystem());
        $this->container()->singleton(Dispatcher::class, fn () => new Dispatcher($app->container()));

        /**
         * IMPORTANT:
         * Your container's alias() signature is: alias($abstract, $alias).
         * Keep this direction consistent across the framework to avoid
         * hard-to-debug binding issues.
         */
        $this->container()->alias(Filesystem::class, 'files');
        $this->container()->alias(Dispatcher::class, 'events');

        // View factory (lazy).
        $this->container()->singleton(ViewFactory::class, function () {
            $files  = $this->container()->make(Filesystem::class);
            $events = $this->container()->make(Dispatcher::class);

            return $this->buildViewFactory($files, $events);
        });

        // Laravel-style string alias: resolve('view') -> ViewFactory
        $this->container()->alias(ViewFactory::class, 'view');

        // Future-proof: bind the contract as well.
        $this->container()->singleton(
            \Illuminate\Contracts\View\Factory::class,
            fn () => $this->container()->make(ViewFactory::class)
        );
    }

    /**
     * Boot phase (post-register).
     *
     * Use this to configure the view factory after it is registered.
     * Keep this lightweight and deterministic.
     */
    public function boot(): void
    {
        try {
            /** @var ViewFactory $view */
            $view = $this->container()->make('view');
        } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
            // Hard fail with a clear message (do not silently degrade).
            throw new \RuntimeException(
                'Blade is not available: container cannot resolve [view]. ' .
                'Make sure ViewServiceProvider::register() bound it before boot().',
                0,
                $e
            );
        }

        // Share commonly-used globals (optional).
        $view->share('app', $this->app);
        $view->share('config', $this->config()->all());
    }

    /**
     * Build the Illuminate view factory.
     *
     * @param  Filesystem $files
     * @param  Dispatcher $events
     * @return ViewFactory
     */
    private function buildViewFactory(Filesystem $files, Dispatcher $events): ViewFactory
    {
        $paths    = (array) $this->config()->get('view.paths', []);
        $compiled = (string) $this->config()->get('view.compiled', '');

        // Normalize and keep only existing directories.
        $paths = $this->normalizePaths($paths);

        // Sensible default if not provided.
        if ($compiled === '') {
            $compiled = $this->app->basePath('storage/framework/views');
        }

        $compiled = $this->normalizePath($compiled);

        // Ensure Blade compiled directory exists.
        // Blade requires this directory to be writable at runtime.
        if ($compiled !== '' && !is_dir($compiled)) {

            $created = mkdir($compiled, 0777, true);

            if (!$created) {
                $debug = (bool) $this->config()->get('debug', false);

                if ($debug) {
                    error_log(
                        '[Yivic Lite Child] WARN: Failed to create Blade compiled directory: ' . $compiled
                    );
                }
            }
        }

        $resolver = new EngineResolver();

        // Blade compiler.
        $bladeCompiler = new BladeCompiler($files, $compiled);

        // Engines: blade + plain php.
        $resolver->register('blade', static fn () => new CompilerEngine($bladeCompiler));
        $resolver->register('php', static fn () => new FileEngine($files));

        /**
         * Explicitly define view file extensions (deterministic behavior).
         * - `test` => `test.blade.php` or `test.php`
         */
        $finder = new FileViewFinder($files, $paths, ['blade.php', 'php']);

        return new ViewFactory($resolver, $finder, $events);
    }

    /**
     * Normalize view search paths and keep only real directories.
     *
     * @param  array $paths
     * @return array
     */
    private function normalizePaths(array $paths): array
    {
        $out = [];

        foreach ($paths as $p) {
            $p = $this->normalizePath((string) $p);
            if ($p !== '' && is_dir($p)) {
                $out[] = $p;
            }
        }

        return $out;
    }

    /**
     * Normalize filesystem paths.
     *
     * - Trim spaces
     * - Convert backslashes to slashes for consistency
     * - Remove trailing slashes
     */
    private function normalizePath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        $path = str_replace('\\', '/', $path);

        return rtrim($path, '/');
    }
}
