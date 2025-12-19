<?php

declare( strict_types = 1 );

$textDomain = 'yivic-lite-child';

return [
    'version'           => YIVIC_LITE_CHILD_VERSION,

    // ------------------------------------------------------------------
    // Child theme root (stylesheet directory)
    // ------------------------------------------------------------------
    // This is the effective runtime root of the active theme.
    // In a child theme context, WordPress resolves:
    // - get_stylesheet_*() → child theme
    // - get_template_*()   → parent theme
    //
    // These values are intentionally overridden here so the child theme
    // can fully control asset loading, view resolution, and translations
    // without relying on WordPress globals elsewhere in the codebase.
    'basePath'          => get_stylesheet_directory(),
    'baseUrl'           => get_stylesheet_directory_uri(),

    // ------------------------------------------------------------------
    // Parent theme root (template directory)
    // ------------------------------------------------------------------
    // Explicitly expose the parent theme's filesystem path and URL.
    //
    // Why this exists:
    // - Enables child → parent fallback for views, templates, and assets
    // - Avoids scattered calls to WordPress global functions at runtime
    // - Keeps the theme kernel fully context-aware and configuration-driven
    //
    // This is especially important for Laravel-style architectures where
    // resolution logic (views/assets) should depend on config, not globals.
    'parentBasePath'    => get_template_directory(),
    'parentBaseUrl'     => get_template_directory_uri(),

    // ------------------------------------------------------------------
    // Internationalization (i18n)
    // ------------------------------------------------------------------
    'textDomain'        => $textDomain,

    // ------------------------------------------------------------------
    // Theme identity
    // ------------------------------------------------------------------
    // Logical identifier for the child theme.
    // Used internally for namespacing, debugging, and instance resolution.
    'themeSlug'         => 'yivic-lite-child',

    // ------------------------------------------------------------------
    // Extension point
    // ------------------------------------------------------------------
    // Reserved for future service providers or runtime bindings.
    // Keep empty by default to avoid unnecessary boot overhead.
    'services'          => [],
];
