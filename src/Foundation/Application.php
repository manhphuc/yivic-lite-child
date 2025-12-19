<?php
declare(strict_types=1);

namespace Yivic\YivicLiteChild\Foundation;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use RuntimeException;

/**
 * Theme Application (Laravel-style).
 *
 * Responsibilities:
 * - Own the DI container instance (Illuminate\Container\Container).
 * - Load and hold runtime configuration (Illuminate\Config\Repository).
 * - Run a bootstrap pipeline (similar to Laravel bootstrappers).
 * - Register and boot service providers (view/database/cache/...).
 *
 * Design goals:
 * - Framework-grade extensibility (CLI/DB/Cache/Queue later without refactor).
 * - Strict separation of concerns (bootstrap vs providers vs kernels).
 * - Config-driven (testable, predictable).
 * - PHP-version friendly: avoid readonly/property-promotion features that require PHP 8.1+.
 */
final class Application
{
    /**
     * Child theme absolute base path (stylesheet directory).
     *
     * Intentionally treated as immutable after construction.
     */
    private string $basePath;

    /**
     * Runtime overrides with highest priority (e.g., forced version/baseUrl).
     *
     * Intentionally treated as immutable after construction.
     *
     * @var array<string, mixed>
     */
    private array $runtimeOverrides;

    /**
     * Container instance for the whole theme runtime.
     */
    private Container $container;

    /**
     * Config repository (merged from wp-app-config/*.php).
     */
    private ConfigRepository $config;

    /**
     * Registered providers (instances), keyed by provider class name.
     *
     * @var array<class-string, \Yivic\YivicLiteChild\Foundation\Providers\ServiceProvider>
     */
    private array $providers = [];

    /**
     * Bootstrap pipeline (order matters).
     *
     * @var array<class-string>
     */
    private array $bootstrappers = [
        \Yivic\YivicLiteChild\Foundation\Bootstrap\LoadConfiguration::class,
        \Yivic\YivicLiteChild\Foundation\Bootstrap\RegisterProviders::class,
        \Yivic\YivicLiteChild\Foundation\Bootstrap\BootProviders::class,
    ];

    /**
     * Create the application.
     *
     * Notes:
     * - We do NOT use `readonly` (PHP 8.1+) to keep the theme portable across environments.
     * - We still treat $basePath and $runtimeOverrides as immutable by convention.
     *
     * @param string $basePath         Child theme absolute path (stylesheet directory).
     * @param array  $runtimeOverrides Optional runtime overrides (highest priority).
     */
    public function __construct(string $basePath, array $runtimeOverrides = [])
    {
        $basePath = rtrim($basePath, '/');

        if ($basePath === '') {
            throw new RuntimeException('Application basePath cannot be empty.');
        }

        $this->basePath         = $basePath;
        $this->runtimeOverrides = $runtimeOverrides;

        $this->container = new Container();

        // Bind core references early (Laravel convention).
        // This makes the app/container/config resolvable during bootstrap.
        $this->container->instance('app', $this);
        $this->container->instance(self::class, $this);
        $this->container->instance(Container::class, $this->container);

        // Config is loaded during bootstrap (LoadConfiguration), but we bind
        // an empty repository now to guarantee the binding exists.
        $this->config = new ConfigRepository([]);
        $this->container->instance('config', $this->config);
        $this->container->instance(ConfigRepository::class, $this->config);
    }

    /**
     * Bootstrap the application.
     *
     * This executes the bootstrap pipeline, resulting in:
     * - config loaded
     * - providers registered
     * - providers booted
     */
    public function bootstrap(): void
    {
        foreach ($this->bootstrappers as $bootstrapperClass) {
            $bootstrapper = new $bootstrapperClass();

            // Keep bootstrapper contract flexible: only require a bootstrap() method.
            if (!method_exists($bootstrapper, 'bootstrap')) {
                continue;
            }

            $bootstrapper->bootstrap($this);
        }
    }

    /**
     * Get the container.
     */
    public function container(): Container
    {
        return $this->container;
    }

    /**
     * Get the configuration repository.
     */
    public function config(): ConfigRepository
    {
        return $this->config;
    }

    /**
     * Build an absolute path from the theme base path.
     *
     * @param string $path Relative path inside theme root.
     */
    public function basePath(string $path = ''): string
    {
        $path = ltrim($path, '/');

        if ($path === '') {
            return $this->basePath;
        }

        return $this->basePath . '/' . $path;
    }

    /**
     * Register a service provider class (single instance).
     *
     * @param class-string<\Yivic\YivicLiteChild\Foundation\Providers\ServiceProvider> $providerClass
     */
    public function registerProvider(string $providerClass): void
    {
        if (isset($this->providers[$providerClass])) {
            return;
        }

        $provider = new $providerClass($this);
        $provider->register();

        $this->providers[$providerClass] = $provider;
    }

    /**
     * Boot all registered providers.
     */
    public function bootProviders(): void
    {
        foreach ($this->providers as $provider) {
            $provider->boot();
        }
    }

    /**
     * Get runtime overrides (highest priority).
     *
     * @return array<string, mixed>
     */
    public function runtimeOverrides(): array
    {
        return $this->runtimeOverrides;
    }
}
