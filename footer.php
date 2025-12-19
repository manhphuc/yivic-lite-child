<?php
defined( 'ABSPATH' ) || exit;

/**
 * WordPress footer bridge.
 *
 * Keep this file minimal:
 * - WordPress expects footer.php in the theme root.
 * - Delegate rendering to Blade templates.
 */
echo theme_view( 'partials.footer' );
?>

</div><!-- .yivic-lite-page__wrap -->

<?php wp_footer(); ?>
</body>
</html>
