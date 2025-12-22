<?php
    /**
     * Footer partial.
     *
     * Block: yivic-lite-footer
     * Elements: __inner, __text
     *
     * Design principles:
     * - Translation via ThemeContext ($theme->__()).
     * - Escaping via ThemeContext helpers.
     * - WordPress APIs are allowed ONLY for data sources (theme_mod),
     *   never for escaping/output.
     */

    /** @var \Yivic\YivicLiteChild\Theme\ThemeContext $theme */
    $theme = $theme ?? app('theme');

    // ---------------------------------------------------------------------
    // Resolve site name (config-first, WP fallback)
    // ---------------------------------------------------------------------
    $siteName = (string) ($config['app.name'] ?? ($config['app']['name'] ?? '') ?: '');
    if ($siteName === '' && function_exists('get_bloginfo')) {
        $siteName = (string) get_bloginfo('name');
    }

    // ---------------------------------------------------------------------
    // Build default copyright (translated, deterministic)
    // ---------------------------------------------------------------------
    $year = function_exists('date_i18n') ? (string) date_i18n('Y') : (string) date('Y');

    $defaultCopyright = sprintf(
        /* translators: 1: current year, 2: site name. */
        $theme->__('© %1$s %2$s. Powered by WordPress & Yivic Lite.'),
        $year,
        $siteName,
    );

    // ---------------------------------------------------------------------
    // Get value from Customizer (content source only)
    // ---------------------------------------------------------------------
    $rawCopyright = function_exists('get_theme_mod')
        ? (string) get_theme_mod('yivic_lite_footer_copyright', $defaultCopyright)
        : $defaultCopyright;

    /**
     * Sanitization policy:
     * - Footer text MAY contain limited HTML (links, <strong>, etc.).
     * - wp_kses_post() is used as a CONTENT SANITIZER (not an escaper).
     * - Final output is trusted HTML.
     */
    $copyright = function_exists('wp_kses_post') ? wp_kses_post($rawCopyright) : $rawCopyright;
?>

<footer class="yivic-lite-footer">
    <div class="yivic-lite-footer__inner">
        <p class="yivic-lite-footer__text">
            <?php echo $copyright; ?>

        </p>
    </div>
</footer>
<?php /**PATH /var/www/html/yivic-codemall/wp-content/themes/yivic-lite-child/resources/views/partials/footer.blade.php ENDPATH**/ ?>