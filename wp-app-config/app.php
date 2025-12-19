<?php

declare( strict_types = 1 );

$textDomain = 'yivic-lite-child';

return [
    'version' => YIVIC_LITE_CHILD_VERSION,

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
    'basePath' => get_stylesheet_directory(),
    'baseUrl'  => get_stylesheet_directory_uri(),

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
    'parentBasePath' => get_template_directory(),
    'parentBaseUrl'  => get_template_directory_uri(),

    /*
    |--------------------------------------------------------------------------
    | Theme Runtime Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the runtime environment the theme is currently
    | operating in. It may be used to control how theme-level features
    | and services are configured (e.g. asset loading strategies).
    |
    | Typical values include: local | staging | production.
    |
    */
    'env' => defined( 'WP_ENV' ) ? WP_ENV : 'production',

    /*
    |--------------------------------------------------------------------------
    | Theme Debug Mode
    |--------------------------------------------------------------------------
    |
    | When debug mode is enabled, the theme may expose additional debug
    | information or enable development-only features.
    |
    | This flag is intentionally centralized here to avoid scattering
    | direct checks against WordPress constants (e.g. WP_DEBUG) throughout
    | the codebase.
    |
    */
    'debug' => defined( 'WP_DEBUG' ) && (bool)WP_DEBUG,

    // ------------------------------------------------------------------
    // Internationalization (i18n)
    // ------------------------------------------------------------------
    'textDomain' => $textDomain,

    // ------------------------------------------------------------------
    // Theme identity
    // ------------------------------------------------------------------
    // Logical identifier for the child theme.
    // Used internally for namespacing, debugging, and instance resolution.
    'themeSlug' => 'yivic-lite-child',

    // ------------------------------------------------------------------
    // Views (Blade / Illuminate\View)
    // ------------------------------------------------------------------
    // Configure view resolution and Blade compilation cache.
    //
    // Why this exists:
    // - Enables Laravel-style rendering: view('home', [...]).
    // - Allows child theme to override templates cleanly.
    // - Provides parent theme fallback without duplicating templates.
    // - Centralizes view paths/cache so the kernel stays config-driven.
    //
    // Resolution order (first match wins):
    // 1) Child theme:  <child>/resources/views
    // 2) Parent theme: <parent>/resources/views
    //
    // Compiled views:
    // - Blade templates (*.blade.php) are compiled into plain PHP and stored
    //   under the "compiled" directory for performance.
    // - This directory must be writable (local/dev) and should be ignored in Git.
    //
    'view' => [
        // View search paths (child-first, then parent as fallback).
        'paths' => [
            get_stylesheet_directory() . '/resources/views',
            get_template_directory()   . '/resources/views',
        ],

        // Blade compiled cache directory.
        // Keep this inside the child theme so overrides remain isolated.
        'compiled' => get_stylesheet_directory() . '/storage/framework/views',
    ],

    // ------------------------------------------------------------------
    // Extension point
    // ------------------------------------------------------------------
    // Reserved for future service providers or runtime bindings.
    // Keep empty by default to avoid unnecessary boot overhead.
    'services' => [],
];
