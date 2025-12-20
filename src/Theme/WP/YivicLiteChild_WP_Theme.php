<?php

declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme\WP;

use Yivic\YivicLiteChild\Foundation\Application as ThemeApplication;
use Yivic\YivicLiteChild\App\Support\Traits\YivicLiteChildTransTrait;
use Yivic\YivicLiteChild\Theme\Concerns\HasThemeIdentity;
use Yivic\YivicLiteChild\Theme\Concerns\HasChildThemeRoots;
use Yivic\YivicLiteChild\Theme\Concerns\HasParentThemeRoots;
use Yivic\YivicLite\Theme\WP\YivicLite_WP_Theme;

/**
 * Class YivicLiteChild_WP_Theme
 *
 * Child Theme kernel for "Yivic Lite".
 *
 * Responsibilities:
 * - Provide child theme metadata (name, version).
 * - Register WordPress hooks specific to the child theme.
 * - Extend and customize behaviors from the parent theme kernel
 *   (YivicLite_WP_Theme) without duplicating logic.
 *
 * Design notes:
 * - This class is intentionally thin.
 * - Heavy logic (services, rendering, integrations) should live in
 *   dedicated services, providers, or traits.
 * - Follows a Laravel-like "kernel" pattern while remaining
 *   100% compatible with WordPress lifecycle.
 */
final class YivicLiteChild_WP_Theme extends YivicLite_WP_Theme {
    use YivicLiteChildTransTrait;
    use HasThemeIdentity;
    use HasParentThemeRoots;
    use HasChildThemeRoots;

    /**
     * @param array $config Runtime configuration for the child theme.
     * @throws \Throwable When the parent kernel fails to initialize.
     */
    public function __construct( array $config = [] ) {
        parent::__construct( $config );

        // Hydrate child-added capabilities.
        $this->hydrateThemeIdentity( $config );
        $this->hydrateParentRoots( $config );

        // ------------------------------------------------------------------
        // Bootstrap Theme Application (DI + Providers)
        // ------------------------------------------------------------------
        // Expose globally for theme-scoped helpers: app(), theme_view(), ...
        //
        // Policy:
        // - Boot once per request.
        // - Runtime config overrides must be passed into Application.
        // - Do NOT overwrite an existing valid app instance.
        // ------------------------------------------------------------------
        $basePath = $this->child_path();

        if ( $basePath === '' && \function_exists( 'get_stylesheet_directory' ) ) {
            $basePath = (string) \get_stylesheet_directory();
        }

        // Application basePath is required; fail fast with a clear exception.
        $application = new ThemeApplication( $basePath, $config );
        $application->bootstrap();

        if (
            ! isset( $GLOBALS[ 'yivic_theme_app' ] ) ||
            ! $GLOBALS[ 'yivic_theme_app' ] instanceof ThemeApplication
        ) {
            $GLOBALS[ 'yivic_theme_app' ] = $application;
        }
    }

    /**
     * Get the text domain for the child theme.
     *
     * This method overrides the default text domain resolution
     * used by translation helpers in the child theme.
     *
     * Priority:
     * - Value from runtime configuration (`textDomain`)
     * - Fallback to the child theme default text domain
     *
     * @return string The text domain used for translations.
     */
    protected function getTextDomain(): string {
        return (string) ( $this->config['textDomain'] ?? 'yivic-lite-child' );
    }

    /**
     * Register WordPress hooks used by the child theme.
     *
     * IMPORTANT:
     * - The parent theme registers its own hooks during its bootstrap.
     * - This method MUST NOT call parent::initTheme(), otherwise parent
     *   hooks would be registered twice, causing UI bugs (e.g. duplicated
     *   menu dropdown arrows).
     *
     * Responsibility:
     * - Attach child-specific hooks only.
     * - Keep this method declarative (no heavy logic).
     *
     * Boot flow:
     * - The child kernel is instantiated and booted explicitly from
     *   the child theme's functions.php (after_setup_theme).
     *
     * Note:
     * - parent::__construct() is still called to initialize parent kernel state,
     *   but we intentionally avoid re-registering parent hooks here.
     */
    public function initTheme(): void {
        add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 20 );

        // Admin assets (separate from frontend).
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ], 20 );
    }

    /**
     * Get the display name of the child theme.
     *
     * This value is used internally by the theme framework
     * and may also be exposed in admin/debug contexts.
     */
    public function get_name(): string {
        return $this->__( 'Yivic Lite Child' );
    }

    /**
     * Get the current version of the child theme.
     *
     * The version constant is defined in functions.php
     * to keep configuration centralized and predictable.
     */
    public function get_version(): string {
        return YIVIC_LITE_CHILD_VERSION;
    }

    /**
     * Configure theme features and supports.
     *
     * Typical usage:
     * - add_theme_support()
     * - register_nav_menus()
     * - image sizes, editor styles, etc.
     *
     * Runs after the parent theme has completed setup.
     */
    public function setup_theme(): void {

        \load_child_theme_textdomain(
            $this->getTextDomain(),
            $this->child_path( 'languages' )
        );

        /**
         * Enable support for site logo (child overrides via filter).
         */
        $filter = $this->get_theme_slug() . '/custom_logo_args';

        add_theme_support(
            'custom-logo',
            apply_filters( $filter, [
                'height'      => 110,
                'width'       => 470,
                'flex-width'  => true,
                'flex-height' => true,
            ] )
        );

        // Allow ordering via `menu_order` where needed.
        add_post_type_support( 'page', 'page-attributes' );
        add_post_type_support( 'post', 'page-attributes' );
    }

    /**
     * Enqueue styles and scripts for the front-end.
     *
     * Best practices:
     * - Use wp_enqueue_style / wp_enqueue_script
     * - Respect dependencies and versioning
     * - Avoid inline logic here (delegate to helpers if needed)
     */
    public function enqueue_scripts(): void {
        $slug    = $this->get_theme_slug();
        $env     = (string) ( $this->config['env'] ?? 'production' );
        $debug   = (bool) ( $this->config['debug'] ?? false );
        $isLocal = ( $env === 'local' );

        // Cache-busting for local/debug builds; stable version for production.
        $version = ( $isLocal || $debug ) ? (string) time() : (string) $this->get_version();

        // Handles (namespaced, readable)
        $styleHandle = $slug . '-main-style';
        $mainHandle  = $slug . '-main-script';

        // Styles
        wp_enqueue_style(
            $styleHandle,
            $this->child_url( 'public-assets/dist/css/main.css' ),
            [],
            $version
        );

        // Scripts
        wp_enqueue_script(
            $mainHandle,
            $this->child_url( 'public-assets/dist/js/main.js' ),
            [],
            $version,
            true
        );

        // Localize into an EXISTING handle.
        wp_localize_script(
            $mainHandle,
            'wpAjax',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
            ]
        );
    }

    /**
     * Enqueue styles and scripts for the WordPress admin area.
     *
     * This method is intentionally separated from front-end asset loading
     * to ensure a clean boundary between:
     * - public-facing assets (handled by enqueue_scripts)
     * - admin-only assets (handled here)
     *
     * Design notes:
     * - Uses the child theme root to avoid leaking parent theme assets.
     * - Applies environment-aware versioning to support cache-busting
     *   in local/debug environments while keeping stable versions in production.
     * - Registers only assets that are guaranteed to exist in the build output
     *   (e.g. admin.css, admin.js).
     *
     * This hook runs on `admin_enqueue_scripts` and should remain lightweight.
     * Heavy logic or conditional admin behavior should be delegated to
     * dedicated admin services or modules.
     */
    public function enqueue_admin_scripts(): void {
        $slug    = $this->get_theme_slug();
        $version = $this->resolveAssetVersion();

        $adminStyleHandle  = $slug . '-admin-style';
        $adminScriptHandle = $slug . '-admin-script';

        wp_enqueue_style(
            $adminStyleHandle,
            $this->child_url( 'public-assets/dist/css/admin.css' ),
            [],
            $version
        );

        wp_enqueue_script(
            $adminScriptHandle,
            $this->child_url( 'public-assets/dist/js/admin.js' ),
            [],
            $version,
            true
        );

        // Optional: if admin.js needs ajax_url
        wp_localize_script(
            $adminScriptHandle,
            'wpAjax',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
            ]
        );
    }

    /**
     * Resolve asset version for cache-busting.
     *
     * Purpose:
     * - Provide a single, centralized strategy for asset versioning.
     * - Prevent stale assets during development and debugging.
     * - Ensure stable, cache-friendly versions in production.
     *
     * Strategy:
     * - Local or debug environment:
     *   → Use the current timestamp (time()) to force cache-busting
     *     on every request.
     * - Production (non-debug):
     *   → Use the theme version to allow long-term browser caching.
     *
     * Design notes:
     * - WordPress accepts both int|string for the `$ver` parameter in
     *   wp_enqueue_style() / wp_enqueue_script().
     * - This method intentionally avoids filesystem or manifest access
     *   to remain fast and deterministic.
     * - Environment flags are read from runtime config to avoid
     *   scattering direct WP_ENV / WP_DEBUG checks across the codebase.
     *
     * @return int|string Asset version suitable for WordPress enqueue APIs.
     */
    private function resolveAssetVersion(): int|string {
        $env     = (string) ( $this->config['env'] ?? 'production' );
        $debug   = (bool) ( $this->config['debug'] ?? false );
        $isLocal = ( $env === 'local' );

        return ( $isLocal || $debug ) ? (string) time() : (string) $this->get_version();
    }

}
