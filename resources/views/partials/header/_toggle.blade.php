{{-- resources/views/partials/header/_toggle.blade.php --}}

<!-- Toggle (mobile) -->
<button
        class="yivic-lite-header__toggle yivicToggle-icon"
        id="yivicMenuToggle"
        type="button"
        aria-expanded="false"
        aria-controls="yivicMobileMenu"
        aria-label="{{ esc_attr__('Open menu', 'yivic-lite') }}"
>
    <img
            class="yivic-lite-header__toggle-icon"
            src="{{ esc_url(get_template_directory_uri() . '/public-assets/dist/img/yivic-lite-toggle-icon.svg') }}"
            alt=""
            aria-hidden="true"
    >
</button>
