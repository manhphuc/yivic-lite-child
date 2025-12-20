<?php

declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme;

use Yivic\YivicLiteChild\Foundation\Application;

/**
 * ThemeContextFactory
 *
 * The ONLY place allowed to build ThemeContext.
 *
 * Responsibilities:
 * - Collect config + runtime inputs.
 * - Normalize / validate paths and URLs.
 * - Provide deterministic defaults and safe fallbacks.
 * - Keep view path rules centralized (avoid duplication in providers).
 *
 * Performance:
 * - Builds once per request (container singleton).
 * - No heavy IO here (no manifest reads, no mkdir).
 */
final class ThemeContextFactory {
    public function make( Application $app ): ThemeContext {
        /** @var array<string,mixed> $cfg */
        $cfg = $app->config()->all();

        // -----------------------------------------------------------------
        // Identity & flags
        // -----------------------------------------------------------------
        $slug       = $this->nonEmptyString( $cfg['themeSlug'] ?? null, 'yivic-lite-child' );
        $textDomain = $this->nonEmptyString( $cfg['textDomain'] ?? null, $slug );

        $env   = $this->nonEmptyString( $cfg['env'] ?? null, 'production' );
        $debug = (bool) ( $cfg['debug'] ?? false ); // keep: intentional boolean normalization

        // -----------------------------------------------------------------
        // Roots (config-first; WP fallback only when config missing)
        // -----------------------------------------------------------------
        $childBasePath = $this->normalizePath(
            $this->nonEmptyString(
                $cfg['basePath'] ?? null,
                \function_exists( 'get_stylesheet_directory' ) ? \get_stylesheet_directory() : $app->basePath()
            )
        );

        $childBaseUrl = $this->normalizeUrl(
            $this->nonEmptyString(
                $cfg['baseUrl'] ?? null,
                \function_exists( 'get_stylesheet_directory_uri' ) ? \get_stylesheet_directory_uri() : ''
            )
        );

        $parentBasePath = $this->normalizePath(
            $this->nonEmptyString(
                $cfg['parentBasePath'] ?? null,
                \function_exists( 'get_template_directory' ) ? \get_template_directory() : ''
            )
        );

        $parentBaseUrl = $this->normalizeUrl(
            $this->nonEmptyString(
                $cfg['parentBaseUrl'] ?? null,
                \function_exists( 'get_template_directory_uri' ) ? \get_template_directory_uri() : ''
            )
        );

        // Optional debug warnings for missing critical roots (do NOT throw).
        if ( $debug ) {
            if ( $childBasePath === '' || $childBaseUrl === '' ) {
                \error_log( '[Yivic Lite Child] WARN: Child roots missing (basePath/baseUrl). Check wp-app-config/app.php' );
            }
            if ( $parentBasePath === '' || $parentBaseUrl === '' ) {
                \error_log( '[Yivic Lite Child] INFO: Parent roots missing (parentBasePath/parentBaseUrl). Parent fallback for views/assets may be unavailable.' );
            }
        }

        // -----------------------------------------------------------------
        // View paths (single source of truth)
        // -----------------------------------------------------------------
        $viewPaths = $this->normalizeViewPaths( $app, $cfg, $childBasePath, $parentBasePath );

        // -----------------------------------------------------------------
        // Compiled views
        // -----------------------------------------------------------------
        $compiled = (string) ( $cfg['view']['compiled'] ?? '' );
        if ( $compiled === '' ) {
            $compiled = $childBasePath !== ''
                ? ( $childBasePath . '/storage/framework/views' )
                : $app->basePath( 'storage/framework/views' );
        }
        $compiled = $this->normalizePath( $compiled );

        return new ThemeContext(
            $app,
            $cfg,
            $slug,
            $textDomain,
            $env,
            $debug,
            $childBasePath,
            $childBaseUrl,
            $parentBasePath,
            $parentBaseUrl,
            $viewPaths,
            $compiled
        );
    }

    /**
     * Normalize view paths and keep only existing directories.
     *
     * @return string[]
     */
    private function normalizeViewPaths( Application $app, array $cfg, string $childBasePath, string $parentBasePath ): array {
        $paths = $cfg['view']['paths'] ?? $cfg['view.paths'] ?? [];
        if ( !\is_array( $paths ) ) {
            $paths = [];
        }

        $out = [];

        // 1) Child default
        $childViews = $childBasePath !== ''
            ? $this->normalizePath( $childBasePath . '/resources/views' )
            : $this->normalizePath( $app->basePath( 'resources/views' ) );

        if ( $childViews !== '' && \is_dir( $childViews ) ) {
            $out[] = $childViews;
        }

        // 2) Config-defined extra paths (keep order)
        foreach ( $paths as $p ) {
            $p = $this->normalizePath( (string) $p );
            if ( $p !== '' && \is_dir( $p ) && !\in_array( $p, $out, true ) ) {
                $out[] = $p;
            }
        }

        // 3) Parent fallback
        if ( $parentBasePath !== '' ) {
            $parentViews = $this->normalizePath( $parentBasePath . '/resources/views' );
            if ( $parentViews !== '' && \is_dir( $parentViews ) && !\in_array( $parentViews, $out, true ) ) {
                $out[] = $parentViews;
            }
        }

        return $out;
    }

    private function nonEmptyString( $value, string $fallback ): string {
        $value = \is_string( $value ) ? \trim( $value ) : '';
        return $value !== '' ? $value : $fallback;
    }

    private function normalizePath( string $path ): string {
        $path = \trim( $path );
        if ( $path === '' ) {
            return '';
        }
        $path = \str_replace( '\\', '/', $path );
        return \rtrim( $path, '/' );
    }

    private function normalizeUrl( string $url ): string {
        $url = \trim( $url );
        if ( $url === '' ) {
            return '';
        }
        return \rtrim( $url, '/' );
    }
}
