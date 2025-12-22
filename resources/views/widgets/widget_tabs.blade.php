{{--
    Widget Tabs (Blade)
    Variables:
    - $title (string)
    - $dom_id (string)
    - $featured (array)
    - $recent (array)
    - $comments (array)
--}}

<header class="yivic-lite-widget__header">
    <h2 class="yivic-lite-widget__title">{{ $title }}</h2>
    <span class="yivic-lite-widget__bar" aria-hidden="true"></span>
</header>

<div class="yivic-lite-widget__body">
    <div class="yivic-lite-tabs" data-yivic-lite-tabs>
        <nav class="yivic-lite-tabs__nav" role="tablist" aria-label="{{ __('Widget tabs', 'yivic-lite-child') }}">
            <button
                class="yivic-lite-tabs__tab is-active"
                type="button"
                role="tab"
                aria-selected="true"
                aria-controls="{{ $dom_id }}-panel-1"
                id="{{ $dom_id }}-tab-1"
                tabindex="0"
            >
                {{ __('Featured', 'yivic-lite-child') }}
            </button>

            <button
                class="yivic-lite-tabs__tab"
                type="button"
                role="tab"
                aria-selected="false"
                aria-controls="{{ $dom_id }}-panel-2"
                id="{{ $dom_id }}-tab-2"
                tabindex="-1"
            >
                {{ __('Recent', 'yivic-lite-child') }}
            </button>

            <button
                class="yivic-lite-tabs__tab"
                type="button"
                role="tab"
                aria-selected="false"
                aria-controls="{{ $dom_id }}-panel-3"
                id="{{ $dom_id }}-tab-3"
                tabindex="-1"
            >
                {{ __('Comments', 'yivic-lite-child') }}
            </button>
        </nav>

        <div class="yivic-lite-tabs__content">
            {{-- Panel 1: Featured --}}
            <section
                class="yivic-lite-tabs__panel is-active"
                id="{{ $dom_id }}-panel-1"
                role="tabpanel"
                aria-labelledby="{{ $dom_id }}-tab-1"
            >
                @if (empty($featured))
                    <p>No featured posts yet.</p>
                @else
                    <ul class="yivic-lite-tablist">
                        @foreach ($featured as $i => $p)
                            <li class="yivic-lite-tablist__item">
                                <span class="yivic-lite-tablist__counter">
                                    {{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}.
                                </span>

                                <div class="yivic-lite-tablist__meta">
                                    @if (! empty($p['cat_name']) && ! empty($p['cat_link']))
                                        <span class="yivic-lite-tablist__badge yivic-lite-tablist__badge--default">
                                            <a class="yivic-lite-tablist__badge-link" href="{{ $p['cat_link'] }}">
                                                {{ $p['cat_name'] }}
                                            </a>
                                        </span>
                                    @endif

                                    <a class="yivic-lite-tablist__title" href="{{ $p['link'] }}">
                                        {{ $p['title'] }}
                                    </a>

                                    @if (! empty($p['date']) && ! empty($p['date_hum']))
                                        <time class="yivic-lite-tablist__date" datetime="{{ $p['date'] }}">
                                            {{ $p['date_hum'] }}
                                        </time>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Panel 2: Recent --}}
            <section
                class="yivic-lite-tabs__panel"
                id="{{ $dom_id }}-panel-2"
                role="tabpanel"
                aria-labelledby="{{ $dom_id }}-tab-2"
                hidden
            >
                @if (empty($recent))
                    <p>No recent posts.</p>
                @else
                    <ul class="yivic-lite-medialist">
                        @foreach ($recent as $p)
                            <li class="yivic-lite-medialist__item">
                                <a
                                    class="yivic-lite-medialist__link"
                                    href="{{ $p['link'] }}"
                                    title="{{ $p['title'] }}"
                                >
                                    @if (! empty($p['thumb']))
                                        <img
                                            class="yivic-lite-medialist__thumb"
                                            src="{{ $p['thumb'] }}"
                                            alt=""
                                            width="100"
                                            height="100"
                                        />
                                    @endif

                                    <span class="yivic-lite-medialist__text">
                                        <strong class="yivic-lite-medialist__strong">{{ $p['title'] }}:</strong>
                                        {{ $p['excerpt'] ?? '' }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Panel 3: Comments --}}
            <section
                class="yivic-lite-tabs__panel"
                id="{{ $dom_id }}-panel-3"
                role="tabpanel"
                aria-labelledby="{{ $dom_id }}-tab-3"
                hidden
            >
                @if (empty($comments))
                    <p>No comments.</p>
                @else
                    <ul class="yivic-lite-medialist">
                        @foreach ($comments as $c)
                            <li class="yivic-lite-medialist__item">
                                <a class="yivic-lite-medialist__link" href="{{ $c['link'] ?? '#' }}" title="Comment">
                                    @if (! empty($c['avatar']))
                                        <img
                                            class="yivic-lite-medialist__thumb yivic-lite-medialist__thumb--avatar"
                                            src="{{ $c['avatar'] }}"
                                            alt=""
                                            width="100"
                                            height="100"
                                        />
                                    @endif

                                    <span class="yivic-lite-medialist__text">
                                        <strong class="yivic-lite-medialist__strong">{{ $c['author'] ?? '' }}:</strong>
                                        {{ $c['text'] ?? '' }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
</div>
