

<?php
    /**
     * Posts loop view (Child theme).
     *
     * Contract:
     * - $query can be injected by caller; if missing, fall back to global $wp_query.
     */
    if (! isset($query) || ! ($query instanceof \WP_Query)) {
        global $wp_query;
        $query = $wp_query;
    }
?>

<div class="yivic-lite-home__list">
    <?php if(! $query || ! $query->have_posts()): ?>
        <p><?php echo e(__('No posts found.', 'yivic-lite-child')); ?></p>
    <?php else: ?>
        <?php while($query->have_posts()): ?>
            <?php
                $query->the_post();
            ?>

            
            <?php echo theme_view('posts._post-item'); ?>

        <?php endwhile; ?>

        
        <?php echo theme_view('partials.pagination._pagination', ['query' => $query]); ?>

    <?php endif; ?>
</div>

<?php
    wp_reset_postdata();
?>
<?php /**PATH /var/www/html/yivic-codemall/wp-content/themes/yivic-lite-child/resources/views/posts/loop.blade.php ENDPATH**/ ?>