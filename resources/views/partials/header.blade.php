{{-- resources/views/partials/header.blade.php --}}
@php
    /**
     * Header (Blade).
     *
     * Responsibilities:
     * - Read theme mods (header background).
     * - Compose header UI from smaller partials.
     *
     * Notes:
     * - Keep logic minimal. Heavy logic should go to a service/controller later.
     */

    $headerBg = get_theme_mod( 'yivic_lite_header_bg_color', '#313b45' );
@endphp

<header id="yivicHeader" class="yivic-lite-header">
    <div id="yivicSticky" class="yivic-lite-header__bar" style="background: {{ $theme->attr( $headerBg ) }};">
        <div class="grid wide">
            <div class="row">
                <div class="col l-12 m-12 c-12">
                    <nav class="yivic-lite-header__nav"
                        role="navigation" aria-label="{{ $theme->attr( $theme->__( 'Primary Menu' ) ) }}">
                        @include( 'partials.header._toggle' )
                        @include( 'partials.header._branding' )
                        @include( 'partials.header._nav', [ 'header_bg' => $headerBg ] )
                        @include( 'partials.header._search' )
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>