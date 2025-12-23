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
    $dom_id   = (string) ( $dom_id ?? 'widget-tabs-' . uniqid() );
    $tabs = [
        1 => ['label' => __('Featured', 'yivic-lite-child')],
        2 => ['label' => __('Recent', 'yivic-lite-child')],
        3 => ['label' => __('Comments', 'yivic-lite-child')],
    ];

    $panelId = fn (int $i) => $dom_id . '-panel-' . $i;
    $tabId   = fn (int $i) => $dom_id . '-tab-' . $i;
@endphp

<header class="yivic-lite-widget__header">
    <h2 class="yivic-lite-widget__title">{{ $title }}</h2>
    <span class="yivic-lite-widget__bar" aria-hidden="true"></span>
</header>

<div class="yivic-lite-widget__body">
    <div class="yivic-lite-tabs" data-yivic-lite-tabs>
        <nav
                class="yivic-lite-tabs__nav"
                role="tablist"
                aria-label="{{ __( 'Widget tabs', 'yivic-lite-child' ) }}"
        >
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
                @forelse ($featured as $i => $p)
                    @if ($loop->first)
                        <ul class="yivic-lite-tablist">
                            @endif

                            @php
                                $titleText = (string) ($p['title'] ?? '');
                                $linkUrl   = (string) ($p['link'] ?? '#');
                                $catName   = (string) ($p['cat_name'] ?? '');
                                $catLink   = (string) ($p['cat_link'] ?? '');
                                $dateIso   = (string) ($p['date'] ?? '');
                                $dateHum   = (string) ($p['date_hum'] ?? '');
                                $counter   = str_pad((string) ((int) $i + 1), 2, '0', STR_PAD_LEFT);
                            @endphp

                            <li class="yivic-lite-tablist__item">
                                <span class="yivic-lite-tablist__counter">{{ $counter }}.</span>

                                <div class="yivic-lite-tablist__meta">
                                    @if ($catName !== '' && $catLink !== '')
                                        <span class="yivic-lite-tablist__badge yivic-lite-tablist__badge--default">
                                    <a class="yivic-lite-tablist__badge-link" href="{{ e($catLink) }}">
                                        {{ $catName }}
                                    </a>
                                </span>
                                    @endif

                                    <a class="yivic-lite-tablist__title" href="{{ e($linkUrl) }}">
                                        {{ $titleText }}
                                    </a>

                                    @if ($dateIso !== '' && $dateHum !== '')
                                        <time class="yivic-lite-tablist__date" datetime="{{ e($dateIso) }}">
                                            {{ $dateHum }}
                                        </time>
                                    @endif
                                </div>
                            </li>

                            @if ($loop->last)
                        </ul>
                    @endif
                @empty
                    <p>{{ __('No featured posts yet.', 'yivic-lite-child') }}</p>
                @endforelse
            </section>

            {{-- Panel 2: Recent --}}
            <section
                    class="yivic-lite-tabs__panel"
                    id="{{ $panelId(2) }}"
                    role="tabpanel"
                    aria-labelledby="{{ $tabId(2) }}"
                    hidden
            >
                @forelse ($recent as $p)
                    @if ($loop->first)
                        <ul class="yivic-lite-medialist">
                            @endif

                            @php
                                $titleText = (string) ($p['title'] ?? '');
                                $linkUrl   = (string) ($p['link'] ?? '#');
                                $thumbUrl  = (string) ($p['thumb'] ?? '');
                                $excerpt   = (string) ($p['excerpt'] ?? '');
                            @endphp

                            <li class="yivic-lite-medialist__item">
                                <a
                                        class="yivic-lite-medialist__link"
                                        href="{{ e($linkUrl) }}"
                                        title="{{ e($titleText) }}"
                                >
                                    @if ($thumbUrl !== '')
                                        <img
                                                class="yivic-lite-medialist__thumb"
                                                src="{{ e($thumbUrl) }}"
                                                alt=""
                                                loading="lazy"
                                                decoding="async"
                                                width="100"
                                                height="100"
                                        />
                                    @endif

                                    <span class="yivic-lite-medialist__text">
                                <strong class="yivic-lite-medialist__strong">{{ $titleText }}</strong>
                                @if ($excerpt !== '')
                                            : {{ $excerpt }}
                                        @endif
                            </span>
                                </a>
                            </li>

                            @if ($loop->last)
                        </ul>
                    @endif
                @empty
                    <p>{{ __('No recent posts.', 'yivic-lite-child') }}</p>
                @endforelse
            </section>

            {{-- Panel 3: Comments --}}
            <section
                    class="yivic-lite-tabs__panel"
                    id="{{ $panelId(3) }}"
                    role="tabpanel"
                    aria-labelledby="{{ $tabId(3) }}"
                    hidden
            >
                @forelse ($comments as $c)
                    @if ($loop->first)
                        <ul class="yivic-lite-medialist">
                            @endif

                            @php
                                $author = (string) ($c['author'] ?? '');
                                $text   = (string) ($c['text'] ?? '');
                                $link   = (string) ($c['link'] ?? '#');
                                $avatar = (string) ($c['avatar'] ?? '');
                                $title  = __('Comment', 'yivic-lite-child');
                            @endphp

                            <li class="yivic-lite-medialist__item">
                                <a class="yivic-lite-medialist__link" href="{{ e($link) }}" title="{{ e($title) }}">
                                    @if ($avatar !== '')
                                        <img
                                                class="yivic-lite-medialist__thumb yivic-lite-medialist__thumb--avatar"
                                                src="{{ e($avatar) }}"
                                                alt=""
                                                loading="lazy"
                                                decoding="async"
                                                width="100"
                                                height="100"
                                        />
                                    @endif

                                    <span class="yivic-lite-medialist__text">
                                @if ($author !== '')
                                            <strong class="yivic-lite-medialist__strong">{{ $author }}</strong>
                                        @endif
                                        @if ($text !== '')
                                            : {{ $text }}
                                        @endif
                            </span>
                                </a>
                            </li>

                            @if ($loop->last)
                        </ul>
                    @endif
                @empty
                    <p>{{ __('No comments.', 'yivic-lite-child') }}</p>
                @endforelse
            </section>
        </div>
    </div>
</div>
