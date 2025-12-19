<?php
defined('ABSPATH') || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('yivic-lite-page'); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary">
    <?php echo esc_html__('Skip to content', 'yivic-lite-child'); ?>
</a>

<div class="yivic-lite-page__wrap">
    <?php echo theme_view( 'partials.header' ); ?>

