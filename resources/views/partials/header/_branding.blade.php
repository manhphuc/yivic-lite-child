{{-- resources/views/partials/header/_branding.blade.php --}}

<!-- Branding / Logo -->
<div class="yivic-lite-header__branding">
    <div class="yivic-lite-header__logo">
        @if (has_custom_logo())
            {!! get_custom_logo() !!}
        @else
            <a href="{{ esc_url(home_url('/')) }}" class="yivic-lite-header__logo-link">
                <span class="yivic-lite-header__site-title">
                    {{ get_bloginfo('name') }}
                </span>
            </a>
        @endif
    </div>
</div>
