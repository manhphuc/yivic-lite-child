{{-- resources/views/partials/header/_branding.blade.php --}}

@php
    /**
     * Branding / Logo partial.
     *
     * Notes:
     * - WP logo HTML is trusted output from WordPress core.
     * - Avoid WP escaping helpers in Blade; use ThemeContext ($theme).
     */

    // Prefer config-driven app name (Laravel style), fallback to WP blog name.
    $siteName = (string) ( ( $config['app.name'] ?? $config['app']['name'] ?? '' ) ?: '' );
    if ( $siteName === '' && function_exists( 'get_bloginfo' ) ) {
        $siteName = (string) get_bloginfo( 'name' );
    }

    // Prefer config-driven home URL if you ever add it later; fallback to WP home_url().
    $homeUrl = function_exists( 'home_url' ) ? (string) home_url('/') : '/';
@endphp

<!-- Branding / Logo -->
<div class="yivic-lite-header__branding">
    <div class="yivic-lite-header__logo">
        @if ( function_exists( 'has_custom_logo' ) && has_custom_logo() )
            {!! get_custom_logo() !!}
        @else
            <a href="{{ $theme->url( $homeUrl ) }}" class="yivic-lite-header__logo-link" aria-label="{{ $theme->attr( $siteName ) }}">
                <span class="yivic-lite-header__site-title">
                    {{ $theme->e( $siteName ) }}
                </span>
            </a>
        @endif
    </div>
</div>
