

<?php
    /**
     * Post item partial (Child theme – Blade).
     *
     * Assumptions:
     * - global $post is already set by $query->the_post()
     */

    // Customizer options
    $show_excerpt = (bool) get_theme_mod('yivic_lite_show_excerpt', true);
    $show_thumb_archive = (bool) get_theme_mod('yivic_lite_show_thumbnail_archive', true);
    $show_archive_avatar = (bool) get_theme_mod('yivic_lite_show_archive_author_avatar', true);
    $show_archive_category = (bool) get_theme_mod('yivic_lite_show_archive_category', true);
    $show_archive_date = (bool) get_theme_mod('yivic_lite_show_archive_date', true);

    // Thumbnail state
    $has_thumb = $show_thumb_archive && has_post_thumbnail();

    // BEM classes
    $post_classes = ['yivic-post', $has_thumb ? 'yivic-post--has-thumb' : 'yivic-post--no-thumb'];

    // First category (for badge)
    $categories = get_the_category();
    $main_cat = ! empty($categories) ? $categories[0] : null;
?>

<article id="post-<?php echo e(get_the_ID()); ?>" <?php echo e(post_class($post_classes, false)); ?>>
    
    <?php if($has_thumb): ?>
        <figure class="yivic-lite-post__thumbnail">
            <a href="<?php echo e(get_permalink()); ?>">
                <?php echo get_the_post_thumbnail(null, 'large'); ?>

            </a>
        </figure>
    <?php endif; ?>

    
    <h2 class="yivic-lite-post__title">
        <a href="<?php echo e(get_permalink()); ?>">
            <?php echo e(get_the_title()); ?>

        </a>
    </h2>

    
    <?php if($show_archive_avatar || $show_archive_category || $show_archive_date): ?>
        <div class="yivic-lite-post__meta">
            
            <?php if($show_archive_avatar): ?>
                <span class="yivic-lite-post__meta-avatar">
                    <?php echo get_avatar(get_the_author_meta('ID'), 24, '', get_the_author(), [
                            'class' => 'yivic-lite-post__meta-avatar-img',
                        ]); ?>

                </span>
            <?php endif; ?>

            
            <?php if($show_archive_category && $main_cat): ?>
                <a
                    class="yivic-lite-post__meta-category"
                    href="<?php echo e(esc_url(get_category_link($main_cat->term_id))); ?>"
                >
                    <?php echo e($main_cat->name); ?>

                </a>
            <?php endif; ?>

            
            <?php if($show_archive_date): ?>
                <span class="yivic-lite-post__meta-date">
                    <?php echo e(get_the_time(get_option('date_format'))); ?>

                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <?php if($show_excerpt): ?>
        <div class="yivic-lite-post__excerpt">
            <?php echo get_the_excerpt(); ?>

        </div>
    <?php endif; ?>
</article>
<?php /**PATH /var/www/html/yivic-codemall/wp-content/themes/yivic-lite-child/resources/views/posts/_post-item.blade.php ENDPATH**/ ?>