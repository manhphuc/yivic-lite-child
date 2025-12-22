

<!-- Search -->
<div class="yivic-lite-header__search search-box">
    <button
        class="yivic-lite-header__search-icon yivicSearch-icon yivicSearch"
        type="button"
        aria-label="<?php echo e($theme->attr($theme->__('Search'))); ?>"
        aria-haspopup="dialog"
        aria-controls="yivic-lite-search-panel"
        aria-expanded="false"
    ></button>

    <div
        id="yivic-lite-search-panel"
        class="yivic-lite-header__search-input input-box"
        role="dialog"
        aria-modal="true"
        aria-hidden="true"
        hidden
    >
        <?php
            // get_search_form echoes by default. Capture it.
            $searchForm = get_search_form(false);
        ?>

        <?php echo $searchForm; ?>

    </div>
</div>
<?php /**PATH /var/www/html/yivic-codemall/wp-content/themes/yivic-lite-child/resources/views/partials/header/_search.blade.php ENDPATH**/ ?>