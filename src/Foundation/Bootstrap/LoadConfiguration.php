<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Foundation\Bootstrap;

use Yivic\YivicLiteChild\Foundation\Application;

/**
 * Load configuration files into the application's config repository.
 *
 * Conventions (Laravel-like):
 * - Configuration lives under: <theme>/wp-app-config/*.php
 * - Each file returns an array.
 * - The file name becomes a top-level config key:
 *   - app.php  => config('app.*')
 *   - view.php => config('view.*')
 *
 * Backward compatibility:
 * - If you currently only have wp-app-config/app.php containing many keys,
 *   we still support that. Later you can split into multiple files.
 */
final class LoadConfiguration {
    public function bootstrap( Application $app ): void {
        $configDir = $app->basePath( 'wp-app-config' );

        $loaded = [];

        // 1) Load app.php first (common practice).
        $appFile = $configDir . '/app.php';
        if ( is_readable( $appFile ) ) {
            $arr = require $appFile;
            if ( is_array( $arr ) ) {
                $loaded = array_replace_recursive( $loaded, $arr );
            }
        }

        // 2) Load other config files (view.php, database.php, cache.php...).
        if ( is_dir( $configDir ) ) {
            $files = glob( $configDir . '/*.php' ) ?: [];
            foreach ($files as $file) {
                $name = basename( (string)$file, '.php' );
                if ( $name === 'app' ) {
                    continue;
                }

                if ( ! is_readable( $file ) ) {
                    continue;
                }

                $arr = require $file;
                if ( ! is_array( $arr ) ) {
                    continue;
                }

                // If file is "view.php", store under "view".
                $loaded[$name] = array_replace_recursive( (array)( $loaded[$name] ?? [] ), $arr );
            }
        }

        // 3) Apply runtime overrides (highest priority).
        $loaded = array_replace_recursive( $loaded, $app->runtimeOverrides() );

        // 4) Put into repository.
        $app->config()->set( $loaded );
    }
}
