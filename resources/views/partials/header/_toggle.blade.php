{{-- resources/views/partials/header/_toggle.blade.php --}}

<!-- Toggle (mobile) -->
<button
        class="yivic-lite-header__toggle yivicToggle-icon"
        id="yivicMenuToggle"
        type="button"
        aria-expanded="false"
        aria-controls="yivicMobileMenu"
        aria-label="{{ $theme->attr( $theme->__( 'Open menu' ) ) }}"
>
    <img
            class="yivic-lite-header__toggle-icon"
            src="{{ $theme->url( $theme->asset( 'public-assets/dist/img/yivic-lite-toggle-icon.svg' ) ) }}"
            alt=""
            aria-hidden="true"
    >
</button>
