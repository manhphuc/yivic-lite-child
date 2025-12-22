{{-- Main layout (Blade) --}}
<div class="yivic-lite-layout">
    <div class="yivic-lite-layout__container grid wide">
        <div class="row">
            <main id="primary" class="{{ $layout['main'] }}">
                {!! $content !!}
            </main>

            @if ($layout['has_sidebar'])
                <aside class="{{ $layout['sidebar'] }}">
                    @if (is_active_sidebar('yivic-lite-sidebar-1'))
                        @php(dynamic_sidebar('yivic-lite-sidebar-1'))
                        {{-- @include( 'tmp.widget' ) --}}
                    @else
                        @include('partials.sidebar')
                    @endif
                </aside>
            @endif
        </div>
    </div>
</div>
