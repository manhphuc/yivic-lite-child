{{--
  Widget Tabs (Blade)

  Variables:
  - $title (string)
  - $dom_id (string)
  - $featured (array<int, array<string, mixed>>)
  - $recent (array<int, array<string, mixed>>)
  - $comments (array<int, array<string, mixed>>)
--}}

@php
    /** @var \Yivic\YivicLiteChild\Theme\ThemeContext $theme */

    $dom_id   = (string) ( $dom_id ?? ( 'widget-tabs-' . uniqid() ) );
    $title    = (string) ( $title ?? '' );

    $featured = is_array( $featured ?? null ) ? $featured : [];
    $recent   = is_array( $recent ?? null ) ? $recent : [];
    $comments = is_array( $comments ?? null ) ? $comments : [];

    $tabs = [
        1 => [ 'label' => $theme->__( 'Featured' ) ],
        2 => [ 'label' => $theme->__( 'Recent' ) ],
        3 => [ 'label' => $theme->__( 'Comments' ) ],
    ];

    $panelId = fn ( int $i ) => $dom_id . '-panel-' . $i;
    $tabId   = fn ( int $i ) => $dom_id . '-tab-' . $i;
@endphp
<div class="yivic-lite-widget yivic-lite-widget--tabs" id="{{ $dom_id }}">
    <header class="yivic-lite-widget__header">
        <h2 class="yivic-lite-widget__title">{{ $title }}</h2>
        <span class="yivic-lite-widget__bar" aria-hidden="true"></span>
    </header>

    <div class="yivic-lite-widget__body">
        <div class="yivic-lite-tabs" data-yivic-lite-tabs>
            <nav class="yivic-lite-tabs__nav" role="tablist" aria-label="{{ $theme->__( 'Widget tabs' ) }}">
                @foreach ($tabs as $i => $t)
                    @php $isActive = ($i === 1); @endphp
                    <button
                            class="yivic-lite-tabs__tab{{ $isActive ? ' is-active' : '' }}"
                            type="button"
                            role="tab"
                            aria-selected="{{ $isActive ? 'true' : 'false' }}"
                            aria-controls="{{ $panelId($i) }}"
                            id="{{ $tabId($i) }}"
                            tabindex="{{ $isActive ? '0' : '-1' }}"
                    >
                        {{ $t['label'] }}
                    </button>
                @endforeach
            </nav>

            <div class="yivic-lite-tabs__content">
                {{-- Panel 1: Featured --}}
                <section
                        class="yivic-lite-tabs__panel is-active"
                        id="{{ $panelId(1) }}"
                        role="tabpanel"
                        aria-labelledby="{{ $tabId(1) }}"
                >
                    @if ( ! empty( $featured ) )
                        <ul class="yivic-lite-tablist">
                            @foreach ( $featured as $i => $p )
                                @php
                                    $p = is_array($p) ? $p : [];

                                    $titleText = (string) ( $p['title'] ?? '' );
                                    $linkUrl   = (string) ( $p['link'] ?? '#' );
                                    $catName   = (string) ( $p['cat_name'] ?? '' );
                                    $catLink   = (string) ( $p['cat_link'] ?? '' );
                                    $dateIso   = (string) ( $p['date'] ?? '' );
                                    $dateHum   = (string) ( $p['date_hum'] ?? '' );

                                    $counter = str_pad( (string) ( (int) $i + 1 ), 2, '0', STR_PAD_LEFT );
                                @endphp

                                <li class="yivic-lite-tablist__item">
                                    <span class="yivic-lite-tablist__counter">{{ $counter }}.</span>

                                    <div class="yivic-lite-tablist__meta">
                                        @if ( $catName !== '' && $catLink !== '' )
                                            <span class="yivic-lite-tablist__badge yivic-lite-tablist__badge--default">
                                                <a class="yivic-lite-tablist__badge-link" href="{{ e( $catLink ) }}">
                                                    {{ $catName }}
                                                </a>
                                            </span>
                                        @endif

                                        <a class="yivic-lite-tablist__title" href="{{ e( $linkUrl ) }}">
                                            {{ $titleText }}
                                        </a>

                                        @if ( $dateIso !== '' && $dateHum !== '' )
                                            <time class="yivic-lite-tablist__date" datetime="{{ e( $dateIso ) }}">
                                                {{ $dateHum }}
                                            </time>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>{{ $theme->__( 'No featured posts yet.' ) }}</p>
                    @endif
                </section>

                {{-- Panel 2: Recent --}}
                <section
                        class="yivic-lite-tabs__panel"
                        id="{{ $panelId(2) }}"
                        role="tabpanel"
                        aria-labelledby="{{ $tabId(2) }}"
                        hidden
                >
                    @if ( ! empty( $recent ) )
                        <ul class="yivic-lite-medialist">
                            @foreach ( $recent as $p )
                                @php
                                    $p = is_array( $p ) ? $p : [];

                                    $titleText = (string) ( $p['title'] ?? '' );
                                    $linkUrl   = (string) ( $p['link'] ?? '#' );
                                    $thumbUrl  = (string) ( $p['thumb'] ?? '' );
                                    $excerpt   = (string) ( $p['excerpt'] ?? '' );
                                @endphp

                                <li class="yivic-lite-medialist__item">
                                    <a class="yivic-lite-medialist__link" href="{{ e( $linkUrl ) }}" title="{{ e( $titleText ) }}">
                                        @if ( $thumbUrl !== '' )
                                            <img
                                                    class="yivic-lite-medialist__thumb"
                                                    src="{{ e( $thumbUrl ) }}"
                                                    alt=""
                                                    loading="lazy"
                                                    decoding="async"
                                                    width="100"
                                                    height="100"
                                            />
                                        @endif

                                        <span class="yivic-lite-medialist__text">
                                            <strong class="yivic-lite-medialist__strong">{{ $titleText }}</strong>
                                            @if ( $excerpt !== '' )
                                                : {{ $excerpt }}
                                            @endif
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>{{ $theme->__( 'No recent posts.' ) }}</p>
                    @endif
                </section>

                {{-- Panel 3: Comments --}}
                <section
                        class="yivic-lite-tabs__panel"
                        id="{{ $panelId(3) }}"
                        role="tabpanel"
                        aria-labelledby="{{ $tabId(3) }}"
                        hidden
                >
                    @if ( ! empty( $comments ) )
                        <ul class="yivic-lite-medialist">
                            @foreach ( $comments as $c )
                                @php
                                    $c = is_array($c) ? $c : [];

                                    $author = (string) ( $c['author'] ?? '' );
                                    $text   = (string) ( $c['text'] ?? '' );
                                    $link   = (string) ( $c['link'] ?? '#' );
                                    $avatar = (string) ( $c['avatar'] ?? '' );
                                    $title  = (string) $theme->__( 'Comment' );
                                @endphp

                                <li class="yivic-lite-medialist__item">
                                    <a class="yivic-lite-medialist__link" href="{{ e( $link ) }}" title="{{ e( $title ) }}">
                                        @if ( $avatar !== '' )
                                            <img
                                                    class="yivic-lite-medialist__thumb yivic-lite-medialist__thumb--avatar"
                                                    src="{{ e( $avatar ) }}"
                                                    alt=""
                                                    loading="lazy"
                                                    decoding="async"
                                                    width="100"
                                                    height="100"
                                            />
                                        @endif

                                        <span class="yivic-lite-medialist__text">
                                            @if ( $author !== '' )
                                                <strong class="yivic-lite-medialist__strong">{{ $author }}</strong>
                                            @endif
                                            @if ( $text !== '' )
                                                : {{ $text }}
                                            @endif
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>{{ $theme->__( 'No comments.' ) }}</p>
                    @endif
                </section>
            </div>
        </div>
    </div>
</div>