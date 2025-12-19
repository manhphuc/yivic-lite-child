<?php

declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme\Concerns;

/**
 * Child theme roots (stylesheet directory) helpers.
 *
 * Uses $this->basePath / $this->baseUrl from the parent kernel config.
 * Keeps path/url concatenation consistent across the codebase.
 */
trait HasChildThemeRoots {
    /**
     * Build a child filesystem path safely.
     */
    public function child_path( string $relative = '' ): string {
        $base       = rtrim( (string)( $this->basePath ?? '' ), '/' );
        $relative   = ltrim( $relative, '/' );

        if ( $base === '' ) {
            return '';
        }

        return $relative === '' ? $base : ( $base . '/' . $relative );
    }

    /**
     * Build a child URL safely.
     */
    public function child_url( string $relative = '' ): string {
        $base       = rtrim( (string)( $this->baseUrl ?? '' ), '/' );
        $relative   = ltrim( $relative, '/' );

        if ( $base === '' ) {
            return '';
        }

        return $relative === '' ? $base : ($base . '/' . $relative);
    }
}
