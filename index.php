<?php
defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Render Home page via child theme view helper.
 *
 * - Keeps WP template thin.
 * - Lets your view resolver handle child-first fallback.
 */
echo theme_view( 'pages.home', [ 'query' => $wp_query ] );

get_footer();
