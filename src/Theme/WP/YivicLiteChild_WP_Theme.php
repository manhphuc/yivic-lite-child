<?php

declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme\WP;

use Yivic\YivicLiteChild\App\Support\Traits\YivicLiteChildTransTrait;
use Yivic\YivicLiteChild\Theme\Concerns\HasThemeIdentity;
use Yivic\YivicLiteChild\Theme\Concerns\HasChildThemeRoots;
use Yivic\YivicLiteChild\Theme\Concerns\HasParentThemeRoots;
use Yivic\YivicLite\Theme\WP\YivicLite_WP_Theme;
use Yivic\YivicLiteChild\Foundation\ThemeApp\ThemeAppRuntime;
use Yivic\YivicLiteChild\Theme\ThemeContext;

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

    private ?ThemeContext $theme = null;

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

        // Bootstrap once per request (fail-fast).
        ThemeAppRuntime::resolve( $basePath, $config );
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
        add_action( 'widgets_init', [ $this, 'register_widgets' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 20 );

        // Admin assets (separate from frontend).
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ], 20 );
    }

    /**
     * Register widget areas + custom widgets.
     */
    public function register_widgets(): void {
        register_sidebar( [
            'name'          => __( 'Sidebar 1', 'yivic-lite-child' ),
            'id'            => 'yivic-lite-child-sidebar-1',
            'description'   => __( 'Main sidebar for Yivic Lite Child.', 'yivic-lite-child' ),

            /**
             * IMPORTANT:
             * - Keep wrapper generic. Do NOT hardcode widget-specific modifier classes here.
             * - `%2$s` will receive widget's own `classname` from WP_Widget.
             */
            'before_widget' => '<section id="%1$s" class="widget yivic-lite-widget %2$s">',
            'after_widget'  => '</section>',

            // Title wrapper used only when widget outputs `$args['before_title']...`
            'before_title'  => '<h2 class="yivic-lite-widget__title">',
            'after_title'   => '</h2>',
        ] );

        // With Composer PSR-4 autoload, this should be available automatically.
        register_widget( \Yivic\YivicLiteChild\Theme\Widgets\YivicLiteChildWidgetTabs::class );
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
     * Enqueue front-end assets for the child theme.
     *
     * Responsibilities:
     * - Define semantic WordPress handles for front-end assets.
     * - Delegate all resolution logic (manifest, versioning, fallbacks)
     *   to enqueueBundle().
     *
     * Design notes:
     * - This method intentionally contains NO environment or filesystem logic.
     * - Asset paths, cache strategy, and manifest usage are centralized
     *   in enqueueBundle() to avoid duplication and drift.
     *
     * Bundle resolved:
     * - CSS: css/main.css   (or hashed equivalent via manifest)
     * - JS:  js/main.js    (or hashed equivalent via manifest)
     *
     * Hooked to:
     * - wp_enqueue_scripts (priority defined by the caller)
     *
     * @return void
     */
    public function enqueue_scripts(): void {
        $slug = $this->get_theme_slug();

        $this->enqueueBundle(
            'main',
            $slug . '-main-style',
            $slug . '-main-script'
        );
    }


    /**
     * Enqueue admin-only assets for the child theme.
     *
     * Responsibilities:
     * - Load assets used exclusively within the WordPress admin area.
     * - Ensure admin assets remain isolated from front-end bundles.
     *
     * Design notes:
     * - Uses the same enqueueBundle() pipeline as front-end assets,
     *   guaranteeing consistent behavior across environments.
     * - Keeps admin asset loading explicit and predictable.
     *
     * Bundle resolved:
     * - CSS: css/admin.css   (or hashed equivalent via manifest)
     * - JS:  js/admin.js    (or hashed equivalent via manifest)
     *
     * Hooked to:
     * - admin_enqueue_scripts
     *
     * @return void
     */
    public function enqueue_admin_scripts(): void {
        $slug = $this->get_theme_slug();

        $this->enqueueBundle(
            'admin',
            $slug . '-admin-style',
            $slug . '-admin-script'
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

    /**
     * Resolve the ThemeContext instance from the application container.
     *
     * Design:
     * - Lazy-loaded to avoid unnecessary container access.
     * - Cached per request to ensure O(1) access after first resolution.
     *
     * Failure strategy:
     * - Fail-fast: if the container cannot resolve ThemeContext,
     *   an exception is allowed to bubble up.
     * - This indicates a misconfigured or broken application state
     *   that should not be silently ignored.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function themeContext(): ThemeContext {
        if ($this->theme === null) {
            $this->theme = ThemeAppRuntime::app()
                ->container()
                ->make(ThemeContext::class);
        }

        return $this->theme;
    }

    /**
     * Enqueue a logical asset bundle (CSS + JS) using a unified, environment-aware strategy.
     *
     * This method centralizes all asset enqueue logic to:
     * - Eliminate duplication between front-end and admin asset loading.
     * - Enforce a single, consistent cache/versioning policy.
     * - Support Laravel-style hashed assets via a Webpack manifest in production.
     *
     * ------------------------------------------------------------------
     * Asset resolution strategy
     * ------------------------------------------------------------------
     *
     * 1) Production environment (env=production, debug=false)
     *    - Attempt to resolve assets via the Webpack manifest
     *      using ThemeContext::mix().
     *    - Filenames are content-hashed (e.g. main.abc123.css),
     *      so NO query-string version is appended.
     *    - If manifest resolution fails for any reason, the method
     *      gracefully falls back to deterministic /dist assets.
     *
     * 2) Development or debug mode
     *    - Always load non-hashed assets directly from:
     *      /public-assets/dist/{css,js}/{scope}.*
     *    - Append a dynamic version (timestamp) to force cache-busting
     *      on every request.
     *
     * This guarantees:
     * - Zero filesystem I/O during normal runtime.
     * - Predictable URLs during development.
     * - Maximum browser cache efficiency in production.
     * - No fatal errors or white screens if the manifest is missing or broken.
     *
     * ------------------------------------------------------------------
     * Security & stability notes
     * ------------------------------------------------------------------
     * - Container access (ThemeContext) is deferred and isolated.
     * - All exceptions during manifest resolution are caught and logged.
     * - The site will always continue to function using fallback assets.
     *
     * ------------------------------------------------------------------
     * Parameters
     * ------------------------------------------------------------------
     *
     * @param string $scope
     *   Logical bundle name without extension.
     *   Examples:
     *   - "main"  → css/main.css + js/main.js
     *   - "admin" → css/admin.css + js/admin.js
     *
     * @param string $styleHandle
     *   Unique WordPress handle for the stylesheet.
     *
     * @param string $scriptHandle
     *   Unique WordPress handle for the script.
     *
     * @return void
     */
    private function enqueueBundle(
        string $scope,
        string $styleHandle,
        string $scriptHandle
    ): void {

        /**
         * Resolve runtime environment flags.
         *
         * - env   : Controls production vs development behavior
         * - debug : Explicitly disables optimizations when enabled
         */
        $env   = (string) ( $this->config['env'] ?? 'production' );
        $debug = (bool)   ( $this->config['debug'] ?? false );

        /**
         * Decide whether to use the Webpack manifest.
         *
         * Manifest-based (hashed) assets are only used when:
         * - Running in production
         * - Debug mode is disabled
         */
        $useManifest = ( $env === 'production' ) && ! $debug;

        /**
         * Asset versioning strategy:
         *
         * - Manifest assets already include a content hash
         *   → DO NOT append a query-string version.
         *
         * - Non-manifest assets (dev/debug)
         *   → Append a dynamic version to force cache-busting.
         */
        $version = $useManifest ? null : $this->resolveAssetVersion();

        /**
         * Default fallback URLs (deterministic, no container required).
         *
         * These are always valid and ensure the site continues to work
         * even if manifest resolution fails.
         */
        $css = $this->child_url( "public-assets/dist/css/{$scope}.css" );
        $js  = $this->child_url( "public-assets/dist/js/{$scope}.js" );

        /**
         * Production path: attempt to resolve hashed assets via manifest.
         *
         * Any failure here MUST NOT break the request.
         * We catch all throwables and fall back to the deterministic paths.
         */
        if ( $useManifest ) {
            try {
                $theme = $this->themeContext();

                $css = $theme->mix( "css/{$scope}.css" );
                $js  = $theme->mix( "js/{$scope}.js" );

            } catch ( \Throwable $e ) {

                /**
                 * Defensive logging only.
                 * Do not expose errors to users or interrupt rendering.
                 */
                if ( \function_exists( 'error_log' ) ) {
                    error_log(
                        '[yivic-lite-child] Asset manifest fallback: ' . $e->getMessage()
                    );
                }

                // Fallback URLs are already assigned above.
            }
        }

        /**
         * Enqueue stylesheet.
         *
         * Dependencies are intentionally left empty to keep this
         * method generic and reusable.
         */
        wp_enqueue_style(
            $styleHandle,
            $css,
            [],
            $version
        );

        /**
         * Enqueue script.
         *
         * - Loaded in the footer for better performance.
         */
        wp_enqueue_script(
            $scriptHandle,
            $js,
            [],
            $version,
            true
        );

        /**
         * Expose WordPress AJAX endpoint to JavaScript.
         *
         * Localized into an EXISTING script handle to avoid
         * accidental script duplication.
         */
        wp_localize_script(
            $scriptHandle,
            'wpAjax',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
            ]
        );
    }

}
