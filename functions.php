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
| We intentionally delay the child kernel bootstrap until `after_setup_theme`
| to ensure the parent theme has completed loading its code (and any internal
| includes/autoload it performs).
|
| Why this matters:
| - Child theme `functions.php` can run before the parent theme's kernel class
|   is available (especially when the parent theme does not expose PSR-4
|   autoloading globally).
| - Booting too early causes a false-negative guard (parent kernel "not loaded")
|   and prevents the child from registering hooks (enqueue, setup, etc.).
|
| Design goals:
| - Config-first bootstrap (runtime config is the single source of truth)
| - Strict guards + fail-fast with debug-only logging
| - No dependency on parent theme file paths (wp.org-safe)
| - No globals leaking (closures only)
|
*/
add_action( 'after_setup_theme', static function (): void {

    $debug = defined( 'WP_DEBUG' ) && WP_DEBUG;

    /*
     * Debug logger (no-op when WP_DEBUG is disabled).
     *
     * Levels:
     * - INFO: successful milestones
     * - WARN: recoverable issues (non-fatal)
     * - FAIL: fatal bootstrap failure (kernel cannot run)
     */
    $log = static function ( string $level, string $message ) use ( $debug ): void {
        if ( ! $debug ) {
            return;
        }
        error_log( sprintf( '[Yivic Lite Child] %s: %s', $level, $message ) );
    };

    /*
     * Guard required classes.
     *
     * At `after_setup_theme`, the parent theme should already be loaded.
     * If the parent kernel is still missing here, it is a real failure
     * (misconfigured parent theme, missing files, or autoload not executed).
     */
    $guardClass = static function ( string $class, string $onFailLevel, string $onFailMessage ) use ( $log ): bool {
        if ( class_exists( $class ) ) {
            return true;
        }
        $log( $onFailLevel, $onFailMessage . ' (' . $class . ')' );
        return false;
    };

    /*
     * Load config from file. The config file MUST return an array.
     * Any invalid config returns an empty array and logs a warning in debug mode.
     */
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

    /*
     * Normalize config: runtime overrides always win.
     * Keep this minimal and deterministic (no side-effects).
     */
    $normalizeConfig = static function ( array $config ): array {
        return array_replace(
            $config,
            [
                'themeFilename' => __FILE__,
            ]
        );
    };

    // ---------------------------------------------------------------------
    // 1) Guards
    // ---------------------------------------------------------------------
    $parentKernel = \Yivic\YivicLite\Theme\WP\YivicLite_WP_Theme::class;
    if ( ! $guardClass( $parentKernel, 'FAIL', 'Parent kernel not loaded' ) ) {
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

    // Optional: validate required config keys here if you want strict mode.
    // Example:
    // if ( empty( $config['basePath'] ) || empty( $config['baseUrl'] ) ) {
    //     $log( 'FAIL', 'Invalid runtime config (basePath/baseUrl).' );
    //     return;
    // }

    // ---------------------------------------------------------------------
    // 3) Boot child kernel (dedicated instance, no singleton collision)
    // ---------------------------------------------------------------------
    try {
        ( new $childKernel( $config ) )->initTheme();
        $log( 'INFO', 'Boot completed.' );
    } catch ( \Throwable $e ) {
        $log( 'FAIL', 'Boot exception: ' . $e->getMessage() );
    }

}, 20 );

