
<div class="yivic-lite-layout">
    <div class="yivic-lite-layout__container grid wide">
        <div class="row">
            <main id="primary" class="<?php echo e($layout['main']); ?>">
                <?php echo $content; ?>

            </main>

            <?php if($layout['has_sidebar']): ?>
                <aside class="<?php echo e($layout['sidebar']); ?>">
                    <?php if(is_active_sidebar('yivic-lite-sidebar-1')): ?>
                        <?php (dynamic_sidebar('yivic-lite-sidebar-1')); ?>
                        <?php echo $__env->make('tmp.widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php else: ?>
                        <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/yivic-codemall/wp-content/themes/yivic-lite-child/resources/views/layouts/main.blade.php ENDPATH**/ ?>