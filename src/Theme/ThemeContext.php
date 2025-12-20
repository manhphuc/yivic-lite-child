<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme;

use Yivic\YivicLiteChild\Foundation\Application;

/**
 * ThemeContext
 *
 * Immutable, configuration-driven runtime facade for templates (Blade).
 *
 * Responsibilities:
 * - Stable identity: slug, textDomain, env, debug.
 * - Normalized roots: child/parent base path + URL.
 * - Deterministic helpers: asset(), mix(), escaping, i18n wrappers.
 *
 * Design principles:
 * - Immutable: built once per request, shared into all views.
 * - Deterministic: same input => same output.
 * - Template-safe: asset/mix never throws; best-effort fallbacks.
 */
final class ThemeContext {
    private Application $app;

    /** @var array<string,mixed> */
    private array $config;

    private string $slug;
    private string $textDomain;
    private string $env;
    private bool $debug;

    private string $childBasePath;
    private string $childBaseUrl;
    private string $parentBasePath;
    private string $parentBaseUrl;

    /** @var string[] */
    private array $viewPaths;

    private string $compiledViewPath;

    /** @var array<string,string>|null */
    private ?array $assetManifest = null;

    /**
     * @param array<string,mixed> $configSnapshot
     * @param string[]           $viewPaths
     */
    public function __construct(
        Application $app,
        array $configSnapshot,
        string $slug,
        string $textDomain,
        string $env,
        bool $debug,
        string $childBasePath,
        string $childBaseUrl,
        string $parentBasePath,
        string $parentBaseUrl,
        array $viewPaths,
        string $compiledViewPath
    ) {
        $this->app              = $app;
        $this->config           = $configSnapshot;

        $this->slug             = $slug;
        $this->textDomain       = $textDomain;
        $this->env              = $env;
        $this->debug            = $debug;

        $this->childBasePath    = $childBasePath;
        $this->childBaseUrl     = $childBaseUrl;
        $this->parentBasePath   = $parentBasePath;
        $this->parentBaseUrl    = $parentBaseUrl;

        $this->viewPaths        = $viewPaths;
        $this->compiledViewPath = $compiledViewPath;
    }

    // ---------------------------------------------------------------------
    // Core accessors
    // ---------------------------------------------------------------------

    public function app(): Application {
        return $this->app;
    }

    /** @return array<string,mixed> */
    public function config(): array {
        return $this->config;
    }

    public function slug(): string {
        return $this->slug;
    }

    public function textDomain(): string {
        return $this->textDomain;
    }

    public function env(): string {
        return $this->env;
    }

    public function debug(): bool {
        return $this->debug;
    }

    // ---------------------------------------------------------------------
    // Roots
    // ---------------------------------------------------------------------

    public function childBasePath(): string {
        return $this->childBasePath;
    }

    public function childBaseUrl(): string {
        return $this->childBaseUrl;
    }

    public function parentBasePath(): string {
        return $this->parentBasePath;
    }

    public function parentBaseUrl(): string {
        return $this->parentBaseUrl;
    }

    public function childPath( string $relative = '' ): string {
        return $this->joinPath( $this->childBasePath, $relative );
    }

    public function childUrl( string $relative = '' ): string {
        return $this->joinUrl( $this->childBaseUrl, $relative );
    }

    public function parentPath( string $relative = '' ): string {
        return $this->joinPath( $this->parentBasePath, $relative );
    }

    public function parentUrl( string $relative = '' ): string {
        return $this->joinUrl( $this->parentBaseUrl, $relative );
    }

    public function hasParentRoots(): bool {
        return $this->parentBasePath !== '' && $this->parentBaseUrl !== '';
    }

    // ---------------------------------------------------------------------
    // Views
    // ---------------------------------------------------------------------

    /** @return string[] */
    public function viewPaths(): array {
        return $this->viewPaths;
    }

    public function compiledViewPath(): string {
        return $this->compiledViewPath;
    }

    /**
     * Ensure compiled directory exists.
     *
     * Policy:
     * - Best-effort only (shared hosting safe).
     * - In debug, log when we cannot create the directory.
     *
     * Implementation notes:
     * - Use 0755 (safer default than 0777).
     * - Race-safe: re-check is_dir() after mkdir fails.
     */
    public function ensureCompiledDirExists(): void {
        $dir = $this->compiledViewPath;

        if ( $dir === '' || \is_dir( $dir ) ) {
            return;
        }

        $ok = @\mkdir( $dir, 0755, true );

        if ( !$ok && !\is_dir( $dir ) && $this->debug ) {
            \error_log( '[Yivic Lite Child] WARN: Cannot create Blade compiled dir: ' . $dir );
        }
    }

    // ---------------------------------------------------------------------
    // Assets
    // ---------------------------------------------------------------------

    /**
     * Resolve an asset URL using child-first, parent-fallback.
     *
     * Rules:
     * - If child file exists -> child URL
     * - Else if parent exists -> parent URL
     * - Else -> child URL (deterministic best-effort)
     *
     * Never throws in templates.
     */
    public function asset( string $relative ): string {
        $relative = $this->sanitizeRelative( $relative );
        if ( $relative === '' ) {
            return $this->childBaseUrl;
        }

        $childPath = $this->childPath( $relative );
        if ( $this->isFileReadable( $childPath ) ) {
            return $this->childUrl( $relative );
        }

        $parentPath = $this->parentPath( $relative );
        if ( $this->isFileReadable( $parentPath ) ) {
            return $this->parentUrl( $relative );
        }

        return $this->childUrl( $relative );
    }

    /**
     * Resolve versioned assets via a manifest mapping (optional).
     *
     * Contract:
     * - $relative is a path relative to the build root (dist), e.g. "css/main.css", "js/main.js".
     * - Manifest maps "css/main.css" => "css/main.abc123.css" (or similar).
     *
     * Behavior:
     * - If manifest missing / invalid / no mapping => fallback to dist asset (non-hashed).
     * - Always serves from the build root: {theme}/public-assets/dist/...
     * - Never throws in templates.
     */
    public function mix(string $relative, string $manifestRelative = ''): string
    {
        $relative = $this->sanitizeRelative($relative);
        if ($relative === '') {
            return $this->childBaseUrl;
        }

        $distRoot = 'public-assets/dist';

        // If caller didn't specify manifest path, try common locations (child-first/parent-fallback handled inside loadAssetManifest()).
        $manifestCandidates = $manifestRelative !== ''
            ? [$manifestRelative]
            : [
                'public-assets/dist/manifest/manifest.json',
                'public-assets/dist/manifest.json',
            ];

        $manifest = null;
        foreach ($manifestCandidates as $candidate) {
            $manifest = $this->loadAssetManifest($candidate);
            if ($manifest !== null && $manifest !== []) {
                break;
            }
        }

        // Manifest missing/empty => fallback to non-hashed asset in dist.
        if ($manifest === null || $manifest === []) {
            return $this->asset($distRoot . '/' . $relative);
        }

        $mapped = $manifest[$relative] ?? null;

        // Mapping missing/invalid => fallback to non-hashed asset in dist.
        if (!\is_string($mapped) || $mapped === '') {
            return $this->asset($distRoot . '/' . $relative);
        }

        $mapped = $this->sanitizeRelative($mapped);
        if ($mapped === '') {
            return $this->asset($distRoot . '/' . $relative);
        }

        return $this->asset($distRoot . '/' . $mapped);
    }

    // ---------------------------------------------------------------------
    // i18n wrappers
    // ---------------------------------------------------------------------

    public function __( string $text ): string {
        if ( \function_exists( '__' ) ) {
            return (string) \__( $text, $this->textDomain );
        }
        return $text;
    }

    public function _e( string $text ): void {
        echo $this->__( $text );
    }

    // ---------------------------------------------------------------------
    // Escaping helpers
    // ---------------------------------------------------------------------

    public function e( $value ): string {
        return \htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
    }

    public function attr( $value ): string {
        return \htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
    }

    public function url( $value ): string {
        return \htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
    }

    // ---------------------------------------------------------------------
    // Internals
    // ---------------------------------------------------------------------

    private function joinPath( string $base, string $relative ): string {
        $base     = $this->normalizePath( $base );
        $relative = $this->sanitizeRelative( $relative );

        if ( $base === '' ) {
            return '';
        }

        return $relative === '' ? $base : ( $base . '/' . $relative );
    }

    private function joinUrl( string $base, string $relative ): string {
        $base     = $this->normalizeUrl( $base );
        $relative = $this->sanitizeRelative( $relative );

        if ( $base === '' ) {
            return '';
        }

        return $relative === '' ? $base : ( $base . '/' . $relative );
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

    /**
     * Sanitize relative path to prevent traversal and keep deterministic behavior.
     *
     * - Trims spaces
     * - Converts backslashes to slashes
     * - Removes leading slashes
     * - Blocks ".." traversal
     */
    private function sanitizeRelative( string $relative ): string {
        $relative = \str_replace( '\\', '/', \trim( $relative ) );
        $relative = \ltrim( $relative, '/' );

        if ( $relative === '' || $relative === '.' ) {
            return '';
        }

        // Hard block traversal attempts (portable across PHP versions).
        if ( $relative === '..' || str_contains( $relative, '../' ) ) {
            return '';
        }

        return $relative;
    }

    private function isFileReadable( string $path ): bool {
        return $path !== '' && @\is_file( $path ) && @\is_readable( $path );
    }

    /**
     * Load manifest once (child-first, parent-fallback).
     *
     * @return array<string,string>|null
     */
    private function loadAssetManifest( string $manifestRelative ): ?array {
        if ( $this->assetManifest !== null ) {
            return $this->assetManifest;
        }

        $manifestRelative = $this->sanitizeRelative( $manifestRelative );
        if ( $manifestRelative === '' ) {
            return $this->assetManifest = null;
        }

        $child  = $this->childPath( $manifestRelative );
        $parent = $this->parentPath( $manifestRelative );

        $json = null;

        if ( $this->isFileReadable( $child ) ) {
            $json = @\file_get_contents( $child );
        } elseif ( $this->isFileReadable( $parent ) ) {
            $json = @\file_get_contents( $parent );
        }

        if ( !\is_string( $json ) || $json === '' ) {
            return $this->assetManifest = null;
        }

        $decoded = \json_decode( $json, true );
        if ( !\is_array( $decoded ) ) {
            return $this->assetManifest = null;
        }

        $out = [];
        foreach ( $decoded as $k => $v ) {
            if ( \is_string($k) && \is_string($v) && $k !== '' && $v !== '' ) {
                // Normalize keys for stability (optional): "js/app.js" vs "/js/app.js"
                $key = \ltrim( $k, '/' );
                $val = \ltrim( $v, '/' );
                $out[$key] = $val;
            }
        }

        return $this->assetManifest = $out;
    }
}
