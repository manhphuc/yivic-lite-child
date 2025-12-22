
<?php
    /**
     * Header (Blade).
     *
     * Responsibilities:
     * - Read theme mods (header background).
     * - Compose header UI from smaller partials.
     *
     * Notes:
     * - Keep logic minimal. Heavy logic should go to a service/controller later.
     */

    $headerBg = get_theme_mod('yivic_lite_header_bg_color', '#313b45');
?>

<header id="yivicHeader" class="yivic-lite-header">
    <div id="yivicSticky" class="yivic-lite-header__bar" style="background: <?php echo e($theme->attr($headerBg)); ?>">
        <div class="grid wide">
            <div class="row">
                <div class="col l-12 m-12 c-12">
                    <nav
                        class="yivic-lite-header__nav"
                        role="navigation"
                        aria-label="<?php echo e($theme->attr($theme->__('Primary Menu'))); ?>"
                    >
                        <?php echo $__env->make('partials.header._toggle', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('partials.header._branding', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('partials.header._nav', ['header_bg' => $headerBg], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('partials.header._search', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<?php /**PATH /var/www/html/yivic-codemall/wp-content/themes/yivic-lite-child/resources/views/partials/header.blade.php ENDPATH**/ ?>