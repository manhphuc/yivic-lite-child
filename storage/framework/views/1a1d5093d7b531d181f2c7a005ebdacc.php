

<header class="yivic-lite-widget__header">
    <h2 class="yivic-lite-widget__title"><?php echo e($title); ?></h2>
    <span class="yivic-lite-widget__bar" aria-hidden="true"></span>
</header>

<div class="yivic-lite-widget__body">
    <div class="yivic-lite-tabs" data-yivic-lite-tabs>
        <nav class="yivic-lite-tabs__nav" role="tablist" aria-label="<?php echo e(__('Widget tabs', 'yivic-lite-child')); ?>">
            <button
                class="yivic-lite-tabs__tab is-active"
                type="button"
                role="tab"
                aria-selected="true"
                aria-controls="<?php echo e($dom_id); ?>-panel-1"
                id="<?php echo e($dom_id); ?>-tab-1"
                tabindex="0"
            >
                <?php echo e(__('Featured', 'yivic-lite-child')); ?>

            </button>

            <button
                class="yivic-lite-tabs__tab"
                type="button"
                role="tab"
                aria-selected="false"
                aria-controls="<?php echo e($dom_id); ?>-panel-2"
                id="<?php echo e($dom_id); ?>-tab-2"
                tabindex="-1"
            >
                <?php echo e(__('Recent', 'yivic-lite-child')); ?>

            </button>

            <button
                class="yivic-lite-tabs__tab"
                type="button"
                role="tab"
                aria-selected="false"
                aria-controls="<?php echo e($dom_id); ?>-panel-3"
                id="<?php echo e($dom_id); ?>-tab-3"
                tabindex="-1"
            >
                <?php echo e(__('Comments', 'yivic-lite-child')); ?>

            </button>
        </nav>

        <div class="yivic-lite-tabs__content">
            
            <section
                class="yivic-lite-tabs__panel is-active"
                id="<?php echo e($dom_id); ?>-panel-1"
                role="tabpanel"
                aria-labelledby="<?php echo e($dom_id); ?>-tab-1"
            >
                <?php if(empty($featured)): ?>
                    <p>No featured posts yet.</p>
                <?php else: ?>
                    <ul class="yivic-lite-tablist">
                        <?php $__currentLoopData = $featured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="yivic-lite-tablist__item">
                                <span class="yivic-lite-tablist__counter">
                                    <?php echo e(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?>.
                                </span>

                                <div class="yivic-lite-tablist__meta">
                                    <?php if(! empty($p['cat_name']) && ! empty($p['cat_link'])): ?>
                                        <span class="yivic-lite-tablist__badge yivic-lite-tablist__badge--default">
                                            <a class="yivic-lite-tablist__badge-link" href="<?php echo e($p['cat_link']); ?>">
                                                <?php echo e($p['cat_name']); ?>

                                            </a>
                                        </span>
                                    <?php endif; ?>

                                    <a class="yivic-lite-tablist__title" href="<?php echo e($p['link']); ?>">
                                        <?php echo e($p['title']); ?>

                                    </a>

                                    <?php if(! empty($p['date']) && ! empty($p['date_hum'])): ?>
                                        <time class="yivic-lite-tablist__date" datetime="<?php echo e($p['date']); ?>">
                                            <?php echo e($p['date_hum']); ?>

                                        </time>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </section>

            
            <section
                class="yivic-lite-tabs__panel"
                id="<?php echo e($dom_id); ?>-panel-2"
                role="tabpanel"
                aria-labelledby="<?php echo e($dom_id); ?>-tab-2"
                hidden
            >
                <?php if(empty($recent)): ?>
                    <p>No recent posts.</p>
                <?php else: ?>
                    <ul class="yivic-lite-medialist">
                        <?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="yivic-lite-medialist__item">
                                <a
                                    class="yivic-lite-medialist__link"
                                    href="<?php echo e($p['link']); ?>"
                                    title="<?php echo e($p['title']); ?>"
                                >
                                    <?php if(! empty($p['thumb'])): ?>
                                        <img
                                            class="yivic-lite-medialist__thumb"
                                            src="<?php echo e($p['thumb']); ?>"
                                            alt=""
                                            width="100"
                                            height="100"
                                        />
                                    <?php endif; ?>

                                    <span class="yivic-lite-medialist__text">
                                        <strong class="yivic-lite-medialist__strong"><?php echo e($p['title']); ?>:</strong>
                                        <?php echo e($p['excerpt'] ?? ''); ?>

                                    </span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </section>

            
            <section
                class="yivic-lite-tabs__panel"
                id="<?php echo e($dom_id); ?>-panel-3"
                role="tabpanel"
                aria-labelledby="<?php echo e($dom_id); ?>-tab-3"
                hidden
            >
                <?php if(empty($comments)): ?>
                    <p>No comments.</p>
                <?php else: ?>
                    <ul class="yivic-lite-medialist">
                        <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="yivic-lite-medialist__item">
                                <a class="yivic-lite-medialist__link" href="<?php echo e($c['link'] ?? '#'); ?>" title="Comment">
                                    <?php if(! empty($c['avatar'])): ?>
                                        <img
                                            class="yivic-lite-medialist__thumb yivic-lite-medialist__thumb--avatar"
                                            src="<?php echo e($c['avatar']); ?>"
                                            alt=""
                                            width="100"
                                            height="100"
                                        />
                                    <?php endif; ?>

                                    <span class="yivic-lite-medialist__text">
                                        <strong class="yivic-lite-medialist__strong"><?php echo e($c['author'] ?? ''); ?>:</strong>
                                        <?php echo e($c['text'] ?? ''); ?>

                                    </span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/yivic-codemall/wp-content/themes/yivic-lite-child/resources/views/widgets/widget_tabs.blade.php ENDPATH**/ ?>