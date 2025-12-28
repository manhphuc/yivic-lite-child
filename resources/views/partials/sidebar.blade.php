{{--
    Default sidebar partial (used when no widgets are active).
    Block: yivic-lite-sidebar
    Elements: __widget, __title, __content
--}}

<div class="yivic-lite-sidebar">
    <div class="yivic-lite-sidebar__widget">
        <h2 class="yivic-lite-sidebar__title">
            {{ $theme->__( 'Sidebar' ) }}
        </h2>

        <div class="yivic-lite-sidebar__content">
            <p>
                {{ $theme->__( 'Add widgets to "Sidebar 1" in Appearance → Widgets.' ) }}
            </p>
        </div>
    </div>
</div>
