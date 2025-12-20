<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Foundation\Bootstrap;

use Yivic\YivicLiteChild\Foundation\Application;

/**
 * Boot all registered providers.
 *
 * Separation rule:
 * - register(): bind things into container
 * - boot():     run post-registration setup (view composers, listeners, etc.)
 */
final class BootProviders {
    public function bootstrap( Application $app ): void {
        $app->bootProviders();
    }
}
