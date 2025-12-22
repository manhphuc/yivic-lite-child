{{-- resources/views/posts/_post-item.blade.php --}}

@php
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
@endphp

<article id="post-{{ get_the_ID() }}" {{ post_class($post_classes, false) }}>
    {{-- Thumbnail --}}
    @if ($has_thumb)
        <figure class="yivic-lite-post__thumbnail">
            <a href="{{ get_permalink() }}">
                {!! get_the_post_thumbnail(null, 'large') !!}
            </a>
        </figure>
    @endif

    {{-- Title --}}
    <h2 class="yivic-lite-post__title">
        <a href="{{ get_permalink() }}">
            {{ get_the_title() }}
        </a>
    </h2>

    {{-- Meta bar --}}
    @if ($show_archive_avatar || $show_archive_category || $show_archive_date)
        <div class="yivic-lite-post__meta">
            {{-- Author avatar --}}
            @if ($show_archive_avatar)
                <span class="yivic-lite-post__meta-avatar">
                    {!!
                        get_avatar(get_the_author_meta('ID'), 24, '', get_the_author(), [
                            'class' => 'yivic-lite-post__meta-avatar-img',
                        ])
                    !!}
                </span>
            @endif

            {{-- Category badge --}}
            @if ($show_archive_category && $main_cat)
                <a
                    class="yivic-lite-post__meta-category"
                    href="{{ esc_url(get_category_link($main_cat->term_id)) }}"
                >
                    {{ $main_cat->name }}
                </a>
            @endif

            {{-- Date --}}
            @if ($show_archive_date)
                <span class="yivic-lite-post__meta-date">
                    {{ get_the_time(get_option('date_format')) }}
                </span>
            @endif
        </div>
    @endif

    {{-- Excerpt --}}
    @if ($show_excerpt)
        <div class="yivic-lite-post__excerpt">
            {!! get_the_excerpt() !!}
        </div>
    @endif
</article>
