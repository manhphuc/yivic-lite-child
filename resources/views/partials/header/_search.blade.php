{{-- resources/views/partials/header/_search.blade.php --}}

<!-- Search -->
<div class="yivic-lite-header__search search-box">
    <button
            class="yivic-lite-header__search-icon yivicSearch-icon yivicSearch"
            type="button"
            aria-label="{{ $theme->attr( $theme->__( 'Search' ) ) }}"
            aria-haspopup="dialog"
            aria-controls="yivic-lite-search-panel"
            aria-expanded="false"
    >
        <img
                class="yivic-lite-header__search-icon-img"
                src="{{ $theme->url( $theme->asset( '/public-assets/dist/img/yivic-lite-search-icon.svg' ) ) }}"
                alt=""
                aria-hidden="true"
        >
    </button>

    <div
            id="yivic-lite-search-panel"
            class="yivic-lite-header__search-input input-box"
            role="dialog"
            aria-modal="true"
            aria-hidden="true"
            hidden
    >
        @php
            // get_search_form echoes by default; capture output for Blade.
            ob_start();
            get_search_form();
            $searchForm = ob_get_clean();
        @endphp

        {!! $searchForm !!}
    </div>
</div>
