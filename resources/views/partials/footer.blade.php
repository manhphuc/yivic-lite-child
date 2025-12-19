@php
    /**
     * Footer partial.
     *
     * Block: yivic-lite-footer
     * Elements: __inner, __text
     */

    $defaultCopyright = sprintf(
        /* translators: 1: current year, 2: site name. */
        __( '© %1$s %2$s. Powered by WordPress & Yivic Lite.', 'yivic-lite-child' ),
        date_i18n( 'Y' ),
        get_bloginfo( 'name' )
    );

    $copyright = get_theme_mod(
        'yivic_lite_footer_copyright',
        $defaultCopyright
    );
@endphp

<footer class="yivic-lite-footer">
    <div class="yivic-lite-footer__inner">
        <p class="yivic-lite-footer__text">
            {!! wp_kses_post( $copyright ) !!}
        </p>
    </div>
</footer>
