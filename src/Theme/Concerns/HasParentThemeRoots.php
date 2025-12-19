<?php

declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme\Concerns;

/**
 * Parent theme roots (template directory).
 *
 * Purpose:
 * - Provide a stable API for child → parent fallback (views/assets/templates).
 * - Prefer config-driven roots for determinism and testability.
 * - Fallback to WordPress functions only when config is absent.
 */
trait HasParentThemeRoots {
    /**
     * Parent theme absolute filesystem path.
     */
    protected string $parent_base_path = '';

    /**
     * Parent theme URL.
     */
    protected string $parent_base_url = '';

    /**
     * Hydrate parent roots once during kernel boot (constructor).
     *
     * Config-first:
     * - parentBasePath
     * - parentBaseUrl
     *
     * WordPress fallback:
     * - get_template_directory()
     * - get_template_directory_uri()
     */
    protected function hydrateParentRoots( array $config ): void {
        $path = (string)( $config['parentBasePath'] ?? $this->config['parentBasePath'] ?? '' );
        $url  = (string)( $config['parentBaseUrl']  ?? $this->config['parentBaseUrl']  ?? '' );

        // Runtime resilience (safe fallback)
        if ( $path === '' && \function_exists( 'get_template_directory' ) ) {
            $path = (string)\get_template_directory();
        }
        if ( $url === '' && \function_exists( 'get_template_directory_uri' ) ) {
            $url = (string)\get_template_directory_uri();
        }

        // Normalize (avoid trailing slash drift)
        $this->parent_base_path = $this->normalizePath($path);
        $this->parent_base_url  = $this->normalizeUrl($url);
    }

    /**
     * Public getter: parent theme absolute path.
     */
    public function get_parent_base_path(): string {
        return $this->parent_base_path;
    }

    /**
     * Public getter: parent theme URL.
     */
    public function get_parent_base_url(): string {
        return $this->parent_base_url;
    }

    /**
     * Build a parent filesystem path safely.
     */
    public function parent_path( string $relative = '' ): string {
        $relative = ltrim( $relative, '/' );
        return $relative === ''
            ? $this->parent_base_path
            : ( $this->parent_base_path !== '' ? $this->parent_base_path . '/' . $relative : '' );
    }

    /**
     * Build a parent URL safely.
     */
    public function parent_url( string $relative = '' ): string {
        $relative = ltrim( $relative, '/' );
        return $relative === ''
            ? $this->parent_base_url
            : ( $this->parent_base_url !== '' ? $this->parent_base_url . '/' . $relative : '' );
    }

    /**
     * Quick integrity check (useful for debugging/health checks).
     */
    public function has_parent_roots(): bool {
        return $this->parent_base_path !== '' && $this->parent_base_url !== '';
    }

    private function normalizePath( string $path ): string {
        $path = trim( $path );
        return $path === '' ? '' : rtrim( $path, '/' );
    }

    private function normalizeUrl( string $url ): string {
        $url = trim( $url );
        return $url === '' ? '' : rtrim( $url, '/' );
    }
}
