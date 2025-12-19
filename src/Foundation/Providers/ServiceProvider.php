<?php
declare(strict_types=1);

namespace Yivic\YivicLiteChild\Foundation\Providers;

use Yivic\YivicLiteChild\Foundation\Application;
use Illuminate\Container\Container;
use Illuminate\Config\Repository as ConfigRepository;

/**
 * Base Service Provider (Laravel-style).
 *
 * Best practices:
 * - register(): bind services into the container (no heavy work).
 * - boot(): perform setup after ALL providers are registered.
 *
 * Keep providers small and focused: one concern per provider.
 */
abstract class ServiceProvider
{
    public function __construct(protected Application $app)
    {
    }

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        // intentionally empty
    }

    /**
     * Boot logic after all providers registered.
     */
    public function boot(): void
    {
        // intentionally empty
    }

    /**
     * Convenience: access container.
     */
    protected function container(): Container
    {
        return $this->app->container();
    }

    /**
     * Convenience: access config repository.
     */
    protected function config(): ConfigRepository
    {
        return $this->app->config();
    }
}
