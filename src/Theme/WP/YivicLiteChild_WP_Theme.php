<?php

declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme\WP;

use Yivic\YivicLiteChild\App\Support\Traits\YivicLiteChildTransTrait;
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

    /**
     * @param array $config Runtime configuration for the child theme.
     * @throws \Throwable When the parent kernel fails to initialize.
     */
    public function __construct( array $config = [] ) {
        parent::__construct( $config );
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
     * This method is called by the parent kernel.
     * Keep it declarative:
     * - only attach hooks
     * - do NOT execute heavy logic here
     */
    public function initTheme(): void {
        parent::initTheme();

        add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
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
        $domain = $this->getTextDomain();

        \load_child_theme_textdomain(
            $domain,
            $this->basePath . '/languages'
        );
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
        // Intentionally left empty.
        // Enqueue child theme assets here when needed.
    }
}
