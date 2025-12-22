<?php
/**
 * Home page view.
 *
 * Responsibilities:
 * - Build the post loop.
 * - Inject it into the main layout.
 *
 * @var WP_Query $query
 */

defined('ABSPATH') || exit();

use Yivic\YivicLite\Theme\Helpers\YivicLiteHelper;

// Render posts loop (child-first, parent fallback)
$loop = theme_view('posts.loop', ['query' => $query]);
$layout = YivicLiteHelper::getLayoutColumns();

// Render main layout and inject loop content
echo theme_view('layouts.main', ['content' => $loop, 'layout' => $layout]);
 ?><?php /**PATH /var/www/html/yivic-codemall/wp-content/themes/yivic-lite-child/resources/views/pages/home.blade.php ENDPATH**/ ?>