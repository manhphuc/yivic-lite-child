<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme\Widgets;

use WP_Widget;
use WP_Query;
use WP_Comment;

defined('ABSPATH') || exit;

/**
 * Tabs Widget (Blade-based).
 *
 * Responsibilities:
 * - Provide a professional tabbed widget UI (Featured / Recent / Comments).
 * - Allow selecting Featured source from Widget admin:
 *   - Sticky posts (default)
 *   - Latest posts
 *   - Category
 *   - Tag
 * - Build small datasets and delegate all markup to Blade for maintainability.
 *
 * Blade view expected:
 * - resources/views/widgets/widget_tabs.blade.php
 * - Rendered via: theme_view('widgets.widget_tabs', $data)
 *
 * Notes:
 * - The sidebar wrapper (before_widget/after_widget) MUST render the outer <section>.
 * - The Blade view should render only inner markup (header/body), NOT another widget wrapper.
 */
final class YivicLiteChildWidgetTabs extends WP_Widget {
    private const FEATURED_SOURCE_STICKY   = 'sticky';
    private const FEATURED_SOURCE_LATEST   = 'latest';
    private const FEATURED_SOURCE_CATEGORY = 'category';
    private const FEATURED_SOURCE_TAG      = 'tag';

    public function __construct() {
        parent::__construct(
                'yivic_lite_child_widget_tabs',
                __( 'Yivic Lite Child: Widget Tabs', 'yivic-lite-child' ),
                [
                    /**
                     * Injected into sidebar wrapper as %2$s (via register_sidebar before_widget).
                     * Do NOT repeat `yivic-lite-widget` here if your sidebar already adds it.
                     */
                        'classname'   => 'yivic-lite-widget--tabs',
                        'description' => __( 'Tabbed widget: Featured / Recent / Comments (Blade).', 'yivic-lite-child' ),
                ]
        );
    }

    /**
     * Front-end output.
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance): void {
        $title = isset( $instance['title'] ) ? (string) $instance['title'] : '';
        $title = $title !== '' ? $title : __( 'Widget Tabs', 'yivic-lite-child' );

        $featuredCount = $this->clamp( (int) ( $instance['featured_count'] ?? 3 ), 1, 10 );
        $recentCount   = $this->clamp( (int) ( $instance['recent_count'] ?? 3 ), 1, 10 );
        $commentCount  = $this->clamp( (int) ( $instance['comment_count'] ?? 3 ), 1, 10 );

        $featuredSource = isset( $instance['featured_source'] ) ? (string) $instance['featured_source'] : self::FEATURED_SOURCE_STICKY;
        $featuredSource = $this->normalizeFeaturedSource( $featuredSource );

        $featuredCatId = isset( $instance['featured_cat_id'] ) ? (int) $instance['featured_cat_id'] : 0;
        $featuredTagId = isset( $instance['featured_tag_id'] ) ? (int) $instance['featured_tag_id'] : 0;

        // Unique DOM id per widget instance render (ARIA ids).
        $domId = 'widget-tabs-' . (int) $this->number . '-' . wp_generate_uuid4();

        // Build featured dataset based on selected source.
        $featured = $this->getFeaturedPosts( $featuredSource, $featuredCount, $featuredCatId, $featuredTagId );

        $data = [
                'title'    => $title,
                'dom_id'   => $domId,
                'featured' => $featured,
                'recent'   => $this->getRecentPosts( $recentCount ),
                'comments' => $this->getRecentComments( $commentCount ),

            // Optional: expose settings to blade if you want labels/badges
                'featured_source' => $featuredSource,
        ];

        echo $args['before_widget'];

        /**
         * Render via Blade.
         *
         * IMPORTANT:
         * - Do NOT do `{!! dynamic_sidebar(...) !!}` inside Blade.
         *   `dynamic_sidebar()` returns boolean; Blade prints "1" when true.
         * - Blade should NOT output another wrapper `<section class="widget">`.
         */
        echo theme_view( 'widgets.widget_tabs', $data );

        echo $args['after_widget'];
    }

    /**
     * Admin form (Widget settings UI).
     */
    public function form( $instance ): void {
        $title         = (string) ( $instance['title'] ?? __( 'Widget Tabs', 'yivic-lite-child' ) );
        $featuredCount = (int) ( $instance['featured_count'] ?? 3 );
        $recentCount   = (int) ( $instance['recent_count'] ?? 3 );
        $commentCount  = (int) ( $instance['comment_count'] ?? 3 );

        $featuredSource = isset( $instance['featured_source'] ) ? (string) $instance['featured_source'] : self::FEATURED_SOURCE_STICKY;
        $featuredSource = $this->normalizeFeaturedSource( $featuredSource );

        $featuredCatId = isset( $instance['featured_cat_id'] ) ? (int) $instance['featured_cat_id'] : 0;
        $featuredTagId = isset( $instance['featured_tag_id'] ) ? (int) $instance['featured_tag_id'] : 0;

        $fieldIdTitle = $this->get_field_id( 'title' );
        $fieldNmTitle = $this->get_field_name( 'title' );

        $fieldIdFeaturedSource = $this->get_field_id( 'featured_source' );
        $fieldNmFeaturedSource = $this->get_field_name( 'featured_source' );

        $fieldIdFeaturedCat = $this->get_field_id( 'featured_cat_id' );
        $fieldNmFeaturedCat = $this->get_field_name( 'featured_cat_id' );

        $fieldIdFeaturedTag = $this->get_field_id( 'featured_tag_id' );
        $fieldNmFeaturedTag = $this->get_field_name( 'featured_tag_id' );

        $fieldIdFeaturedCount = $this->get_field_id( 'featured_count' );
        $fieldNmFeaturedCount = $this->get_field_name( 'featured_count' );

        $fieldIdRecentCount = $this->get_field_id( 'recent_count' );
        $fieldNmRecentCount = $this->get_field_name( 'recent_count' );

        $fieldIdCommentCount = $this->get_field_id( 'comment_count' );
        $fieldNmCommentCount = $this->get_field_name( 'comment_count' );

        // Load terms for dropdowns.
        $categories = get_categories( [
                'hide_empty' => false,
        ] );

        $tags = get_tags( [
                'hide_empty' => false,
        ] );

        ?>
        <p>
            <label for="<?php echo esc_attr( $fieldIdTitle ); ?>">
                <?php esc_html_e( 'Title', 'yivic-lite-child' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $fieldIdTitle ); ?>"
                   name="<?php echo esc_attr( $fieldNmTitle ); ?>"
                   type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr( $fieldIdFeaturedSource ); ?>">
                <?php esc_html_e( 'Featured source', 'yivic-lite-child' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $fieldIdFeaturedSource ); ?>"
                    name="<?php echo esc_attr( $fieldNmFeaturedSource ); ?>"
                    data-yivic-featured-source>
                <option value="<?php echo esc_attr( self::FEATURED_SOURCE_STICKY ); ?>" <?php selected( $featuredSource, self::FEATURED_SOURCE_STICKY ); ?>>
                    <?php esc_html_e( 'Sticky posts', 'yivic-lite-child' ); ?>
                </option>
                <option value="<?php echo esc_attr( self::FEATURED_SOURCE_LATEST ); ?>" <?php selected( $featuredSource, self::FEATURED_SOURCE_LATEST ); ?>>
                    <?php esc_html_e( 'Latest posts', 'yivic-lite-child' ); ?>
                </option>
                <option value="<?php echo esc_attr( self::FEATURED_SOURCE_CATEGORY ); ?>" <?php selected( $featuredSource, self::FEATURED_SOURCE_CATEGORY ); ?>>
                    <?php esc_html_e( 'Category', 'yivic-lite-child' ); ?>
                </option>
                <option value="<?php echo esc_attr( self::FEATURED_SOURCE_TAG ); ?>" <?php selected( $featuredSource, self::FEATURED_SOURCE_TAG ); ?>>
                    <?php esc_html_e( 'Tag', 'yivic-lite-child' ); ?>
                </option>
            </select>
        </p>

        <p data-yivic-featured-cat style="<?php echo ( $featuredSource === self::FEATURED_SOURCE_CATEGORY ) ? '' : 'display:none;'; ?>">
            <label for="<?php echo esc_attr( $fieldIdFeaturedCat ); ?>">
                <?php esc_html_e( 'Featured category', 'yivic-lite-child' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $fieldIdFeaturedCat ); ?>"
                    name="<?php echo esc_attr( $fieldNmFeaturedCat ); ?>">
                <option value="0"><?php esc_html_e( '— Select category —', 'yivic-lite-child' ); ?></option>
                <?php foreach ( $categories as $cat ) : ?>
                    <option value="<?php echo (int) $cat->term_id; ?>" <?php selected( $featuredCatId, (int) $cat->term_id ); ?>>
                        <?php echo esc_html( $cat->name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p data-yivic-featured-tag style="<?php echo ( $featuredSource === self::FEATURED_SOURCE_TAG ) ? '' : 'display:none;'; ?>">
            <label for="<?php echo esc_attr( $fieldIdFeaturedTag ); ?>">
                <?php esc_html_e( 'Featured tag', 'yivic-lite-child' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $fieldIdFeaturedTag ); ?>"
                    name="<?php echo esc_attr( $fieldNmFeaturedTag ); ?>">
                <option value="0"><?php esc_html_e( '— Select tag —', 'yivic-lite-child' ); ?></option>
                <?php foreach ( $tags as $tag ) : ?>
                    <option value="<?php echo (int) $tag->term_id; ?>" <?php selected( $featuredTagId, (int) $tag->term_id ); ?>>
                        <?php echo esc_html( $tag->name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr( $fieldIdFeaturedCount ); ?>">
                <?php esc_html_e('Featured items', 'yivic-lite-child'); ?>
            </label>
            <input class="tiny-text"
                   id="<?php echo esc_attr( $fieldIdFeaturedCount ); ?>"
                   name="<?php echo esc_attr( $fieldNmFeaturedCount ); ?>"
                   type="number" step="1" min="1" max="10"
                   value="<?php echo esc_attr( (string) $featuredCount ); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr( $fieldIdRecentCount ); ?>">
                <?php esc_html_e( 'Recent items', 'yivic-lite-child' ); ?>
            </label>
            <input class="tiny-text"
                   id="<?php echo esc_attr( $fieldIdRecentCount ); ?>"
                   name="<?php echo esc_attr( $fieldNmRecentCount ); ?>"
                   type="number" step="1" min="1" max="10"
                   value="<?php echo esc_attr( (string) $recentCount ); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr( $fieldIdCommentCount ); ?>">
                <?php esc_html_e( 'Comment items', 'yivic-lite-child' ); ?>
            </label>
            <input class="tiny-text"
                   id="<?php echo esc_attr( $fieldIdCommentCount ); ?>"
                   name="<?php echo esc_attr( $fieldNmCommentCount ); ?>"
                   type="number" step="1" min="1" max="10"
                   value="<?php echo esc_attr( (string) $commentCount ); ?>">
        </p>

        <script>
            ( function(){
                const root = document.currentScript?.closest('form') || document;
                const sourceSelect = root.querySelector( '[data-yivic-featured-source]' );
                const catRow = root.querySelector( '[data-yivic-featured-cat]' );
                const tagRow = root.querySelector( '[data-yivic-featured-tag]' );
                if ( !sourceSelect || !catRow || !tagRow ) return;

                function sync() {
                    const v = sourceSelect.value;
                    catRow.style.display = ( v === '<?php echo esc_js(self::FEATURED_SOURCE_CATEGORY); ?>' ) ? '' : 'none';
                    tagRow.style.display = ( v === '<?php echo esc_js(self::FEATURED_SOURCE_TAG); ?>' ) ? '' : 'none';
                }
                sourceSelect.addEventListener( 'change', sync );
                sync();
            } )();
        </script>
        <?php
    }

    /**
     * Persist settings safely.
     */
    public function update($new_instance, $old_instance): array {
        $instance = [];

        $instance['title'] = isset( $new_instance['title'] )
                ? sanitize_text_field( (string) $new_instance['title'] )
                : '';

        $source = isset( $new_instance[ 'featured_source' ] ) ? (string) $new_instance[ 'featured_source' ] : self::FEATURED_SOURCE_STICKY;
        $instance['featured_source'] = $this->normalizeFeaturedSource($source);

        $instance['featured_cat_id'] = isset( $new_instance[ 'featured_cat_id' ] ) ? absint( $new_instance[ 'featured_cat_id' ] ) : 0;
        $instance['featured_tag_id'] = isset( $new_instance[ 'featured_tag_id' ] ) ? absint( $new_instance[ 'featured_tag_id' ] ) : 0;

        $instance['featured_count'] = $this->clamp( (int) ( $new_instance['featured_count'] ?? 3 ), 1, 10 );
        $instance['recent_count']   = $this->clamp( (int) ( $new_instance['recent_count'] ?? 3 ), 1, 10 );
        $instance['comment_count']  = $this->clamp( (int) ( $new_instance['comment_count'] ?? 3 ), 1, 10 );

        $this->flushCache();

        return $instance;
    }

    /* -----------------------------
     * Data providers
     * ----------------------------- */

    private function getFeaturedPosts( string $source, int $limit, int $catId, int $tagId ): array {
        // Cache key includes chosen source + term id (if any) to avoid collisions.
        $termPart = 0;

        if ( $source === self::FEATURED_SOURCE_CATEGORY ) {
            $termPart = $catId;
        } elseif ( $source === self::FEATURED_SOURCE_TAG ) {
            $termPart = $tagId;
        }

        $cacheKey = $this->cacheKey( 'featured', $limit, $source, $termPart );
        $cached   = get_transient( $cacheKey );
        if ( is_array( $cached ) ) {
            return $cached;
        }

        $posts = [];

        switch ( $source ) {
            case self::FEATURED_SOURCE_CATEGORY:
                if ( $catId > 0 ) {
                    $posts = $this->queryPosts( [
                            'posts_per_page' => $limit,
                            'post_status'    => 'publish',
                            'no_found_rows'  => true,
                            'cat'            => $catId,
                    ], false );
                }
                break;

            case self::FEATURED_SOURCE_TAG:
                if ( $tagId > 0 ) {
                    $posts = $this->queryPosts( [
                            'posts_per_page' => $limit,
                            'post_status'    => 'publish',
                            'no_found_rows'  => true,
                            'tax_query'      => [
                                    [
                                            'taxonomy' => 'post_tag',
                                            'field'    => 'term_id',
                                            'terms'    => [$tagId],
                                    ],
                            ],
                    ], false );
                }
                break;

            case self::FEATURED_SOURCE_LATEST:
                $posts = $this->queryPosts( [
                        'posts_per_page' => $limit,
                        'post_status'    => 'publish',
                        'no_found_rows'  => true,
                ], false );
                break;

            case self::FEATURED_SOURCE_STICKY:
            default:
                $sticky = get_option( 'sticky_posts', [] );
                $sticky = is_array( $sticky ) ? array_values( $sticky ) : [];
                $sticky = array_slice( $sticky, 0, $limit );

                if ( ! empty( $sticky ) ) {
                    $posts = $this->queryPosts( [
                            'post__in'            => $sticky,
                            'orderby'             => 'post__in',
                            'posts_per_page'      => $limit,
                            'ignore_sticky_posts' => 1,
                            'no_found_rows'       => true,
                            'post_status'         => 'publish',
                    ], false );
                }
                break;
        }

        // Optional fallback (recommended): if chosen source yields nothing, fallback to latest.
        if ( empty( $posts ) ) {
            $posts = $this->queryPosts( [
                    'posts_per_page' => $limit,
                    'post_status'    => 'publish',
                    'no_found_rows'  => true,
            ], false );
        }

        set_transient( $cacheKey, $posts, 5 * MINUTE_IN_SECONDS );

        return $posts;
    }

    private function getRecentPosts( int $limit ): array {
        $cacheKey = $this->cacheKey( 'recent', $limit );
        $cached   = get_transient( $cacheKey );
        if ( is_array( $cached ) ) {
            return $cached;
        }

        $posts = $this->queryPosts( [
                'posts_per_page' => $limit,
                'post_status'    => 'publish',
                'no_found_rows'  => true,
        ], true );

        set_transient( $cacheKey, $posts, 5 * MINUTE_IN_SECONDS );

        return $posts;
    }

    private function getRecentComments( int $limit ): array {
        $cacheKey = $this->cacheKey('comments', $limit);
        $cached   = get_transient($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $comments = get_comments([
                'number' => $limit,
                'status' => 'approve',
                'type'   => 'comment',
        ]);

        $items = [];

        foreach ( $comments as $c ) {
            /** @var WP_Comment $c */
            $items[] = [
                    'author' => (string) $c->comment_author,
                    'text'   => wp_trim_words( wp_strip_all_tags( (string) $c->comment_content ), 14, '…' ),
                    'link'   => (string) get_comment_link( $c ),
                    'avatar' => (string) get_avatar_url( $c, [ 'size' => 100 ] ),
            ];
        }

        set_transient( $cacheKey, $items, 5 * MINUTE_IN_SECONDS );

        return $items;
    }

    /**
     * Run a WP_Query and map results to lightweight arrays.
     *
     * @param array $queryArgs
     * @param bool  $withExcerpt
     * @return array<int, array<string, mixed>>
     */
    private function queryPosts( array $queryArgs, bool $withExcerpt ): array {
        $q = new WP_Query($queryArgs);

        $posts = [];
        while ( $q->have_posts() ) {
            $q->the_post();
            $posts[] = $this->mapPost( (int) get_the_ID(), $withExcerpt );
        }

        wp_reset_postdata();

        return $posts;
    }

    private function mapPost( int $postId, bool $withExcerpt ): array {
        $cat = get_the_category( $postId );
        $cat = ! empty( $cat ) ? $cat[0] : null;

        return [
                'id'       => $postId,
                'title'    => (string) get_the_title( $postId ),
                'link'     => (string) get_permalink( $postId ),
                'date'     => (string) get_the_date( 'Y-m-d', $postId ),
                'date_hum' => (string) get_the_date( get_option( 'date_format' ), $postId ),
                'cat_name' => (string) ($cat?->name ?? ''),
                'cat_link' => $cat ? (string) get_category_link( $cat->term_id ) : '',
                'thumb'    => (string) ( get_the_post_thumbnail_url($postId, 'thumbnail') ?: '' ),
                'excerpt'  => $withExcerpt ? (string) wp_trim_words( get_the_excerpt( $postId ), 16, '…' ) : '',
        ];
    }

    /* -----------------------------
     * Utilities
     * ----------------------------- */

    private function clamp( int $value, int $min, int $max ): int {
        return max($min, min($max, $value));
    }

    private function normalizeFeaturedSource( string $source ): string {
        $allowed = [
                self::FEATURED_SOURCE_STICKY,
                self::FEATURED_SOURCE_LATEST,
                self::FEATURED_SOURCE_CATEGORY,
                self::FEATURED_SOURCE_TAG,
        ];

        return in_array( $source, $allowed, true ) ? $source : self::FEATURED_SOURCE_STICKY;
    }

    private function cacheKey( string $group, int $limit, string $source = '', int $termId = 0 ): string {
        $blogId = (int) get_current_blog_id();

        $parts = [
                'yivic_lite_child_tabs',
                (string) $blogId,
                $group,
                (string) $limit,
        ];

        if ( $source !== '' ) {
            $parts[] = $source;
        }

        if ( $termId > 0 ) {
            $parts[] = (string) $termId;
        }

        return implode('_', $parts);
    }

    private function flushCache(): void {
        // Clear known ranges; keep it simple and safe.
        $sources = [
                self::FEATURED_SOURCE_STICKY,
                self::FEATURED_SOURCE_LATEST,
                self::FEATURED_SOURCE_CATEGORY,
                self::FEATURED_SOURCE_TAG,
        ];

        foreach ( range( 1, 10 ) as $n ) {
            delete_transient( $this->cacheKey( 'recent', $n ) );
            delete_transient( $this->cacheKey( 'comments', $n ) );

            // featured keys: many variants (source + term), clear common ones
            foreach ( $sources as $s ) {
                delete_transient( $this->cacheKey( 'featured', $n, $s, 0 ) );
            }
        }
    }
}
