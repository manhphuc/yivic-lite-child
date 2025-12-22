{{-- resources/views/partials/pagination/_pagination.blade.php --}}

@php
    /**
     * Pagination partial (Blade).
     *
     * @var \WP_Query|null $query
     */

    // Guard: valid query
    if (! ($query instanceof \WP_Query)) {
        return;
    }

    // No pagination needed
    if ($query->max_num_pages <= 1) {
        return;
    }

    $current = max(1, get_query_var('paged'));

    $pagination = paginate_links([
        'total' => (int) $query->max_num_pages,
        'current' => $current,
        'mid_size' => 2,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'type' => 'list', // <ul>…</ul>
    ]);
@endphp

@if (! empty($pagination))
    <nav class="yivic-pagination" aria-label="{{ esc_attr__('Posts pagination', 'yivic-lite-child') }}">
        {!! wp_kses_post($pagination) !!}
    </nav>
@endif
