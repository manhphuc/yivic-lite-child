<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme\Widgets;

use WP_Query;
use WP_Comment;
use Yivic\YivicLiteChild\App\Support\Widget\YivicWidgetBase;

defined( 'ABSPATH' ) || exit;

/**
 * Tabs Widget (Blade-based, schema-driven admin form).
 *
 * - Featured / Recent / Comments
 * - Featured source: Sticky | Latest | Category | Tag
 * - No query inside Blade view (data provider pattern)
 *
 * Blade view:
 * - resources/views/widgets/widget_tabs.blade.php
 * - Rendered via: theme_view('widgets.widget_tabs', $data)
 */
final class YivicLiteChildWidgetTabs extends YivicWidgetBase {
    protected const CACHE_NS                = 'yivic_lite_child_tabs';
    private const FEATURED_SOURCE_STICKY    = 'sticky';
    private const FEATURED_SOURCE_LATEST    = 'latest';
    private const FEATURED_SOURCE_CATEGORY  = 'category';
    private const FEATURED_SOURCE_TAG       = 'tag';

    public function __construct() {
        parent::__construct(
            'yivic_lite_child_widget_tabs',
            __( 'Yivic Lite Child: Widget Tabs', 'yivic-lite-child' ),
            [
                'classname'   => 'yivic-lite-widget--tabs',
                'description' => __( 'Tabbed widget: Featured / Recent / Comments (Blade).', 'yivic-lite-child' ),
            ]
        );
    }

    protected function view(): string {
        return 'widgets.widget_tabs';
    }

    /**
     * Build Blade data (front-end).
     *
     * @param array $args Sidebar args
     * @param array $instance Widget instance settings
     * @return array<string, mixed>
     */
    protected function buildData( array $args, array $instance ): array {
        $values = $this->valuesFromInstance( $instance );

        // Unique DOM id per render (ARIA ids).
        $domId = 'widget-tabs-' . (int) $this->number . '-' . wp_generate_uuid4();

        $featured = $this->getFeaturedPosts(
            (string) $values['featured_source'],
            (int) $values['featured_count'],
            (int) $values['featured_cat_id'],
            (int) $values['featured_tag_id']
        );

        return [
            'title'          => (string) $values['title'],
            'dom_id'         => $domId,
            'featured'       => $featured,
            'recent'         => $this->getRecentPosts( (int) $values['recent_count'] ),
            'comments'       => $this->getRecentComments( (int) $values['comment_count'] ),
            'featured_source'=> (string) $values['featured_source'],
        ];
    }

    /**
     * Keep form() super short: values + schema only.
     *
     * @param array $instance
     * @return array<string, mixed>
     */
    protected function valuesFromInstance( array $instance ): array {
        $title              = $this->resolveTitle( $instance, __( 'Widget Tabs', 'yivic-lite-child' ) );
        $featuredSource     = isset( $instance['featured_source'] )
                ? (string) $instance['featured_source']
                : self::FEATURED_SOURCE_STICKY;
        $featuredSource     = $this->normalizeFeaturedSource( $featuredSource );

        return [
            'title'           => $title,

            'featured_source' => $featuredSource,
            'featured_cat_id' => isset( $instance['featured_cat_id'] ) ? (int) $instance['featured_cat_id'] : 0,
            'featured_tag_id' => isset( $instance['featured_tag_id'] ) ? (int) $instance['featured_tag_id'] : 0,

            'featured_count'  => $this->clampInt( (int) ( $instance['featured_count'] ?? 3 ), 1, 10 ),
            'recent_count'    => $this->clampInt( (int) ( $instance['recent_count'] ?? 3 ), 1, 10 ),
            'comment_count'   => $this->clampInt( (int) ( $instance['comment_count'] ?? 3 ), 1, 10 ),
        ];
    }

    /**
     * Schema-driven admin UI.
     *
     * @param array<string, mixed> $values
     * @return array<int, array<string, mixed>>
     */
    protected function schema( array $values ): array {
        // Prepare select options here (provider), NOT in Blade.
        $categories         = get_categories( [ 'hide_empty' => false ] );
        $categoryOptions    = [];
        foreach ( $categories as $cat ) {
            $categoryOptions[ (string) (int) $cat->term_id ] = (string) $cat->name;
        }

        $tags       = get_tags( [ 'hide_empty' => false ] );
        $tagOptions = [];
        foreach ( $tags as $tag ) {
            $tagOptions[ (string) (int) $tag->term_id ] = (string) $tag->name;
        }

        return [
                [
                    'type'  => 'text',
                    'key'   => 'title',
                    'label' => __( 'Title', 'yivic-lite-child' ),
                    'attrs' => [ 'class' => 'widefat' ],
                ],
                [
                    'type'    => 'select',
                    'key'     => 'featured_source',
                    'label'   => __( 'Featured source', 'yivic-lite-child' ),
                    'attrs'   => [ 'class' => 'widefat' ],
                    'options' => [
                        self::FEATURED_SOURCE_STICKY   => __( 'Sticky posts', 'yivic-lite-child' ),
                        self::FEATURED_SOURCE_LATEST   => __( 'Latest posts', 'yivic-lite-child' ),
                        self::FEATURED_SOURCE_CATEGORY => __( 'Category', 'yivic-lite-child' ),
                        self::FEATURED_SOURCE_TAG      => __( 'Tag', 'yivic-lite-child' ),
                    ],
                ],
                [
                    'type'    => 'select',
                    'key'     => 'featured_cat_id',
                    'label'   => __( 'Featured category', 'yivic-lite-child' ),
                    'when'    => [ 'featured_source' => self::FEATURED_SOURCE_CATEGORY ],
                    'empty'   => [ 'enabled' => true, 'label' => __( '— Select category —', 'yivic-lite-child' ) ],
                    'options' => $categoryOptions,
                    'attrs'   => [ 'class' => 'widefat' ],
                ],
                [
                    'type'    => 'select',
                    'key'     => 'featured_tag_id',
                    'label'   => __( 'Featured tag', 'yivic-lite-child' ),
                    'when'    => [ 'featured_source' => self::FEATURED_SOURCE_TAG ],
                    'empty'   => [ 'enabled' => true, 'label' => __( '— Select tag —', 'yivic-lite-child' ) ],
                    'options' => $tagOptions,
                    'attrs'   => [ 'class' => 'widefat' ],
                ],
                [
                    'type'  => 'number',
                    'key'   => 'featured_count',
                    'label' => __( 'Featured items', 'yivic-lite-child' ),
                    'attrs' => [ 'min' => 1, 'max' => 10, 'class' => 'tiny-text' ],
                ],
                [
                    'type'  => 'number',
                    'key'   => 'recent_count',
                    'label' => __( 'Recent items', 'yivic-lite-child' ),
                    'attrs' => [ 'min' => 1, 'max' => 10, 'class' => 'tiny-text' ],
                ],
                [
                    'type'  => 'number',
                    'key'   => 'comment_count',
                    'label' => __( 'Comment items', 'yivic-lite-child' ),
                    'attrs' => [ 'min' => 1, 'max' => 10, 'class' => 'tiny-text' ],
                ],
        ];
    }

    /**
     * Persist widget settings safely.
     */
    public function update( $new_instance, $old_instance ): array {
        $new = (array) $new_instance;

        $instance = [];

        $instance['title'] = isset( $new['title'] )
                ? sanitize_text_field( (string) $new['title'] )
                : '';

        $source = isset( $new['featured_source'] )
                ? (string) $new['featured_source']
                : self::FEATURED_SOURCE_STICKY;

        $instance['featured_source'] = $this->normalizeFeaturedSource( $source );

        $instance['featured_cat_id'] = isset( $new['featured_cat_id'] ) ? absint( $new['featured_cat_id'] ) : 0;
        $instance['featured_tag_id'] = isset( $new['featured_tag_id'] ) ? absint( $new['featured_tag_id'] ) : 0;

        $instance['featured_count'] = $this->clampInt( (int) ( $new['featured_count'] ?? 3 ), 1, 10 );
        $instance['recent_count']   = $this->clampInt( (int) ( $new['recent_count'] ?? 3 ), 1, 10 );
        $instance['comment_count']  = $this->clampInt( (int) ( $new['comment_count'] ?? 3 ), 1, 10 );

        $this->flushCache();

        return $instance;
    }

    /* -----------------------------------------
     * Data providers (cached)
     * ----------------------------------------- */

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getFeaturedPosts( string $source, int $limit, int $catId, int $tagId ): array {
        $termPart = 0;
        if ( $source === self::FEATURED_SOURCE_CATEGORY ) {
            $termPart = $catId;
        } elseif ( $source === self::FEATURED_SOURCE_TAG ) {
            $termPart = $tagId;
        }

        $cacheKey = $this->cacheKey( 'featured', [ $limit, $source, $termPart ] );
        $cached = get_transient( $cacheKey );
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

        // Fallback: if no posts, use latest.
        if ( empty( $posts ) ) {
            $posts = $this->queryPosts( [
                    'posts_per_page' => $limit,
                    'post_status'    => 'publish',
                    'no_found_rows'  => true,
            ], false );
        }

        set_transient( $cacheKey, $posts, static::CACHE_TTL );

        return $posts;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getRecentPosts( int $limit ): array {
        $cacheKey = $this->cacheKey( 'recent', [$limit] );
        $cached = get_transient( $cacheKey );
        if ( is_array( $cached ) ) {
            return $cached;
        }

        $posts = $this->queryPosts( [
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ], true );

        set_transient( $cacheKey, $posts, static::CACHE_TTL );

        return $posts;
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function getRecentComments( int $limit ): array {
        $cacheKey = $this->cacheKey( 'comments', [$limit] );
        $cached = get_transient( $cacheKey );
        if ( is_array( $cached ) ) {
            return $cached;
        }

        $comments = get_comments( [
            'number' => $limit,
            'status' => 'approve',
            'type'   => 'comment',
        ] );

        $items = [];

        foreach ( $comments as $c ) {
            /** @var WP_Comment $c */
            $items[] = [
                'author' => (string) $c->comment_author,
                'text'   => wp_trim_words(wp_strip_all_tags((string) $c->comment_content), 14, '…'),
                'link'   => (string) get_comment_link($c),
                'avatar' => (string) get_avatar_url($c, ['size' => 100]),
            ];
        }

        set_transient( $cacheKey, $items, static::CACHE_TTL );

        return $items;
    }

    /**
     * Run WP_Query and map results to lightweight arrays.
     *
     * @param array<string, mixed> $queryArgs
     * @return array<int, array<string, mixed>>
     */
    private function queryPosts( array $queryArgs, bool $withExcerpt ): array {
        $q = new WP_Query( $queryArgs );

        $posts = [];

        while ( $q->have_posts() ) {
            $q->the_post();
            $posts[] = $this->mapPost( (int) get_the_ID(), $withExcerpt );
        }

        wp_reset_postdata();

        return $posts;
    }

    /**
     * @return array<string, mixed>
     */
    private function mapPost( int $postId, bool $withExcerpt ): array {
        $cat = get_the_category( $postId );
        $cat = ! empty( $cat ) ? $cat[0] : null;

        return [
            'id'       => $postId,
            'title'    => (string) get_the_title( $postId ),
            'link'     => (string) get_permalink( $postId ),

            'date'     => (string) get_the_date( 'Y-m-d', $postId ),
            'date_hum' => (string) get_the_date( get_option( 'date_format' ), $postId ),

            'cat_name' => (string) ( $cat?->name ?? '' ),
            'cat_link' => $cat ? (string) get_category_link( $cat->term_id ) : '',

            'thumb'    => (string) ( get_the_post_thumbnail_url($postId, 'thumbnail') ?: '' ),
            'excerpt'  => $withExcerpt ? (string) wp_trim_words( get_the_excerpt( $postId ), 16, '…' ) : '',
        ];
    }

    /* -----------------------------------------
     * Utilities
     * ----------------------------------------- */

    private function normalizeFeaturedSource( string $source ): string {
        $allowed = [
            self::FEATURED_SOURCE_STICKY,
            self::FEATURED_SOURCE_LATEST,
            self::FEATURED_SOURCE_CATEGORY,
            self::FEATURED_SOURCE_TAG,
        ];

        return in_array( $source, $allowed, true ) ? $source : self::FEATURED_SOURCE_STICKY;
    }

    private function flushCache(): void {
        $sources = [
            self::FEATURED_SOURCE_STICKY,
            self::FEATURED_SOURCE_LATEST,
            self::FEATURED_SOURCE_CATEGORY,
            self::FEATURED_SOURCE_TAG,
        ];

        foreach ( range( 1, 10 ) as $n ) {
            delete_transient( $this->cacheKey( 'recent', [$n] ) );
            delete_transient( $this->cacheKey( 'comments', [$n] ) );

            foreach ( $sources as $s ) {
                delete_transient( $this->cacheKey( 'featured', [ $n, $s, 0 ] ) );
            }
        }
    }
}
