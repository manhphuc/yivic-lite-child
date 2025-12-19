<?php
/*
 * Theme Name:     Yivic Lite Child
 * Theme URI:      https://github.com/manhphuc/yivic-lite-child
 * Description:    Child theme for Yivic Lite
 * Author:         Phuc Nguyen
 * Author URI:     https://codemall.org
 * Template:       yivic-lite
 * Version:        1.0.0
 * Text Domain:    yivic-lite-child
 */

defined( 'ABSPATH' ) || exit;

/*
|--------------------------------------------------------------------------
| 1. Theme Constants
|--------------------------------------------------------------------------
*/
defined( 'YIVIC_LITE_CHILD_VERSION' ) || define( 'YIVIC_LITE_CHILD_VERSION', '1.0.0' );
defined( 'YIVIC_LITE_CHILD_SLUG' )    || define( 'YIVIC_LITE_CHILD_SLUG', 'yivic-lite-child' );


/*
|--------------------------------------------------------------------------
| 2. Autoload Strategy
|--------------------------------------------------------------------------
|
| This theme supports two autoloading mechanisms to ensure it works
| reliably in all environments (development, production, WordPress.org).
|
| 1) Composer Autoloader (Preferred)
|    - Used during local development or when the theme is installed
|      manually by developers.
|    - Provides full PSR-4 support and dependency management.
|
| 2) Lightweight Fallback Autoloader
|    - Used when the theme is distributed via WordPress.org and the
|      /vendor directory is removed.
|    - Implements a minimal PSR-4-compatible autoloader for the
|      Yivic\YivicLiteChild\ namespace only.
|
| If neither autoloader is available, the theme kernel CANNOT function
| safely. In that case, we:
| - Log a clear error message in debug mode.
| - Stop execution early to avoid fatal errors or undefined behavior.
|
| This approach guarantees:
| - Predictable bootstrap behavior
| - Safe failure in broken environments
| - Zero hard dependency on Composer in production
|
*/
$composerAutoload = __DIR__ . '/vendor/autoload.php';
$fallbackAutoload = __DIR__ . '/src/YivicLiteChild_Theme_Autoloader.php';

if ( is_readable( $composerAutoload ) ) {

    // Preferred path: Composer-based autoloading (development / packaged builds)
    require_once $composerAutoload;

} elseif ( is_readable( $fallbackAutoload ) ) {

    // Fallback path: lightweight PSR-4 autoloader (WordPress.org distribution)
    require_once $fallbackAutoload;

} else {

    // Autoloader missing entirely → fail fast and log in debug mode
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log(
            '[Yivic Lite Child] ERROR: No autoloader found. Checked:' . PHP_EOL .
            '- ' . $composerAutoload . PHP_EOL .
            '- ' . $fallbackAutoload
        );
    }

    // Stop cleanly: the theme kernel cannot run without an autoloader
    return;
}

/*
|--------------------------------------------------------------------------
| 3. Bootstrap Child Kernel
|--------------------------------------------------------------------------
|
| Architect-style bootloader:
| - isolate scope (no globals leaking)
| - strict guards (fail-fast)
| - normalized config (runtime overrides win)
| - consistent logging
|
*/
(static function (): void {

    $debug = defined( 'WP_DEBUG' ) && WP_DEBUG;

    $log = static function ( string $level, string $message ) use ( $debug ): void {
        if ( ! $debug ) {
            return;
        }
        // Levels: INFO | WARN | FAIL | SKIP
        error_log( sprintf( '[Yivic Lite Child] %s: %s', $level, $message ) );
    };

    $guardClass = static function ( string $class, string $onFailLevel, string $onFailMessage ) use ( $log ): bool {
        if ( class_exists( $class ) ) {
            return true;
        }
        $log( $onFailLevel, $onFailMessage . ' (' . $class . ')' );
        return false;
    };

    $loadConfig = static function ( string $file ) use ( $log ): array {
        if ( ! is_readable( $file ) ) {
            $log( 'WARN', 'Config file not readable: ' . $file );
            return [];
        }

        $loaded = require $file;

        if ( is_array( $loaded ) ) {
            return $loaded;
        }

        $log( 'WARN', 'Config must return an array: ' . $file );
        return [];
    };

    $normalizeConfig = static function ( array $config ): array {
        // Runtime overrides (source of truth for critical meta)
        return array_replace( $config, [
            'themeFilename' => __FILE__,
        ] );
    };

    // ---------------------------------------------------------------------
    // 1) Guards
    // ---------------------------------------------------------------------
    $parentKernel = \Yivic\YivicLite\Theme\WP\YivicLite_WP_Theme::class;
    if ( ! $guardClass( $parentKernel, 'SKIP', 'Parent kernel not loaded' ) ) {
        return;
    }

    $childKernel = \Yivic\YivicLiteChild\Theme\WP\YivicLiteChild_WP_Theme::class;
    if ( ! $guardClass( $childKernel, 'FAIL', 'Child kernel class not found' ) ) {
        return;
    }

    // ---------------------------------------------------------------------
    // 2) Load + normalize config
    // ---------------------------------------------------------------------
    $configFile = __DIR__ . '/wp-app-config/app.php';
    $config     = $normalizeConfig( $loadConfig( $configFile ) );

    // Optional: if you require certain keys, validate here (architect habit)
    // Example:
    // if ( empty( $config['basePath'] ) || empty( $config['baseUrl'] ) ) {
    //     $log( 'FAIL', 'Invalid runtime config (basePath/baseUrl).' );
    //     return;
    // }

    // ---------------------------------------------------------------------
    // 3) Boot kernel (dedicated instance, no singleton collision)
    // ---------------------------------------------------------------------
    try {
        ( new $childKernel( $config ) )->initTheme();
        $log( 'INFO', 'Boot completed.' );
    } catch ( \Throwable $e ) {
        $log( 'FAIL', 'Boot exception: ' . $e->getMessage() );
    }

})();
