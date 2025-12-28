{{-- resources/views/widgets/widget_tagcloud.blade.php --}}

@php
    /** @var \Yivic\YivicLiteChild\Theme\ThemeContext $theme */
    $title = $title ?? $theme->__( 'Tag Cloud' );
    $domId = $dom_id ?? ( 'widget-tagcloud-' . uniqid() );
    $items = is_array( $items ?? null ) ? $items : [];
@endphp

<div class="yivic-lite-widget yivic-lite-widget--tagcloud" id="{{ $domId }}">
    <header class="yivic-lite-widget__header">
        <h2 class="yivic-lite-widget__title">
            {{ $title }}
        </h2>
        <span class="yivic-lite-widget__bar" aria-hidden="true"></span>
    </header>

    <div class="yivic-lite-widget__body">
        @if ( ! empty( $items ) )
            <div class="yivic-tagcloud" role="list" aria-label="{{ $title }}">
                @foreach ( $items as $it )
                    <a class="yivic-tagcloud__item"
                       href="{{ $it['link'] ?? '#' }}"
                       aria-label="{{ ($it['name'] ?? '') . ' (' . (string)( $it['count'] ?? 0 ) . ')' }}">
                        {{ $it['name'] ?? '' }}
                    </a>
                @endforeach
            </div>
        @else
            <p class="yivic-lite-widget__empty">
                {{ $theme->__( 'No tags found.' ) }}
            </p>
        @endif
    </div>
</div>
