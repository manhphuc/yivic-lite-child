{{-- resources/views/posts/loop.blade.php --}}

@php
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
@endphp

<div class="yivic-lite-home__list">
    @if (! $query || ! $query->have_posts())
        <p>{{ __('No posts found.', 'yivic-lite-child') }}</p>
    @else
        @while ($query->have_posts())
            @php
                $query->the_post();
            @endphp

            {{-- Render a single post item partial --}}
            {!! theme_view('posts._post-item') !!}
        @endwhile

        {{-- Pagination partial --}}
        {!! theme_view('partials.pagination._pagination', ['query' => $query]) !!}
    @endif
</div>

@php
    wp_reset_postdata();
@endphp
