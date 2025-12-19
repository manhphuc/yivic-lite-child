<?php

declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme\Concerns;

/**
 * Theme identity concern.
 *
 * Provides a stable, config-driven theme identifier (slug).
 * Used for namespacing, debugging, logging, and future DI/view resolution.
 */
trait HasThemeIdentity {
    /**
     * Logical theme slug.
     */
    protected string $theme_slug = '';

    /**
     * Hydrate theme identity from runtime config.
     */
    protected function hydrateThemeIdentity( array $config ): void {
        $slug = (string)( $config['themeSlug'] ?? $this->config['themeSlug'] ?? '' );

        // Fallback (should rarely happen, but keeps runtime safe)
        if ( $slug === '' ) {
            $slug = defined( 'YIVIC_LITE_CHILD_SLUG' )
                ? YIVIC_LITE_CHILD_SLUG
                : 'yivic-lite-child';
        }

        $this->theme_slug = $slug;
    }

    /**
     * Public accessor for theme slug.
     */
    public function get_theme_slug(): string {
        return $this->theme_slug;
    }
}
