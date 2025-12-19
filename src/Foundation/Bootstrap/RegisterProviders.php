<?php
declare(strict_types=1);

namespace Yivic\YivicLiteChild\Foundation\Bootstrap;

use Yivic\YivicLiteChild\Foundation\Application;

/**
 * Register service providers.
 *
 * Providers are the main extension mechanism (Laravel-style):
 * - ViewServiceProvider
 * - DatabaseServiceProvider (later)
 * - CacheServiceProvider (later)
 * - ConsoleServiceProvider (later)
 *
 * Source of truth:
 * - config('providers') if present
 * - otherwise, use a sensible default set
 */
final class RegisterProviders
{
    public function bootstrap(Application $app): void
    {
        $providers = $app->config()->get('providers');

        if (!is_array($providers) || $providers === []) {
            $providers = [
                \Yivic\YivicLiteChild\Foundation\Providers\ViewServiceProvider::class,
            ];
        }

        foreach ($providers as $providerClass) {
            if (!is_string($providerClass) || $providerClass === '') {
                continue;
            }
            $app->registerProvider($providerClass);
        }
    }
}
