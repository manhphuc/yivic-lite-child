<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\Theme\Widgets;

use Yivic\YivicLiteChild\App\Support\Widget\YivicWidgetBase;

defined( 'ABSPATH' ) || exit;

/**
 * Tag Cloud Widget (Blade-based, schema-driven admin form).
 *
 * Blade view:
 * - resources/views/widgets/widget_tagcloud.blade.php
 * - Rendered via: theme_view('widgets.widget_tagcloud', $data)
 */
final class YivicLiteChildWidgetTagCloud extends YivicWidgetBase {
    protected const CACHE_NS = 'yivic_lite_child_tagcloud';

    private const TAXONOMY_TAG      = 'post_tag';
    private const TAXONOMY_CATEGORY = 'category';

    public function __construct() {
        parent::__construct(
            'yivic_lite_child_widget_tagcloud',
            __( 'Yivic Lite Child: Tag Cloud (Blade)', 'yivic-lite-child' ),
            [
                'classname'   => 'yivic-lite-widget--tagcloud',
                'description' => __( 'Tag cloud widget rendered via Blade.', 'yivic-lite-child' ),
            ]
        );
    }

    protected function view(): string {
        return 'widgets.widget_tagcloud';
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
        $domId = 'widget-tagcloud-' . (int) $this->number . '-' . wp_generate_uuid4();

        $items = $this->getCloudItems(
            (string) $values['taxonomy'],
            (int) $values['number'],
            (string) $values['orderby'],
            (string) $values['order']
        );

        return [
            'title'    => (string) $values['title'],
            'dom_id'   => $domId,
            'taxonomy' => (string) $values['taxonomy'],
            'items'    => $items,
        ];
    }

    /**
     * @param array $instance
     * @return array<string, mixed>
     */
    protected function valuesFromInstance( array $instance ): array {
        $title    = $this->resolveTitle( $instance, __( 'Tag Cloud', 'yivic-lite-child' ) );

        $taxonomy = isset( $instance['taxonomy'] ) ? (string) $instance['taxonomy'] : self::TAXONOMY_TAG;
        $taxonomy = $this->normalizeTaxonomy( $taxonomy );

        $number   = $this->clampInt( (int) ( $instance['number'] ?? 20 ), 1, 100 );

        $orderby  = isset( $instance['orderby'] ) ? (string) $instance['orderby'] : 'count';
        $orderby  = in_array( $orderby, [ 'count', 'name' ], true ) ? $orderby : 'count';

        $order    = isset( $instance['order'] ) ? strtoupper( (string) $instance['order'] ) : 'DESC';
        $order    = in_array( $order, [ 'ASC', 'DESC' ], true ) ? $order : 'DESC';

        return [
            'title'    => $title,
            'taxonomy' => $taxonomy,
            'number'   => $number,
            'orderby'  => $orderby,
            'order'    => $order,
        ];
    }

    /**
     * Schema-driven admin UI.
     *
     * @param array<string, mixed> $values
     * @return array<int, array<string, mixed>>
     */
    protected function schema( array $values ): array {
        return [
            [
                'type'  => 'text',
                'key'   => 'title',
                'label' => __( 'Title', 'yivic-lite-child' ),
                'attrs' => [ 'class' => 'widefat' ],
            ],
            [
                'type'    => 'select',
                'key'     => 'taxonomy',
                'label'   => __( 'Taxonomy', 'yivic-lite-child' ),
                'attrs'   => [ 'class' => 'widefat' ],
                'options' => [
                    self::TAXONOMY_TAG      => __( 'Tags', 'yivic-lite-child' ),
                    self::TAXONOMY_CATEGORY => __( 'Categories', 'yivic-lite-child' ),
                ],
            ],
            [
                'type'  => 'number',
                'key'   => 'number',
                'label' => __( 'Items', 'yivic-lite-child' ),
                'attrs' => [ 'min' => 1, 'max' => 100, 'class' => 'tiny-text' ],
            ],
            [
                'type'    => 'select',
                'key'     => 'orderby',
                'label'   => __( 'Order by', 'yivic-lite-child' ),
                'attrs'   => [ 'class' => 'widefat' ],
                'options' => [
                    'count' => __( 'Count', 'yivic-lite-child' ),
                    'name'  => __( 'Name', 'yivic-lite-child' ),
                ],
            ],
            [
                'type'    => 'select',
                'key'     => 'order',
                'label'   => __( 'Order', 'yivic-lite-child' ),
                'attrs'   => [ 'class' => 'widefat' ],
                'options' => [
                    'DESC' => __( 'DESC', 'yivic-lite-child' ),
                    'ASC'  => __( 'ASC', 'yivic-lite-child' ),
                ],
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

        $taxonomy = isset( $new['taxonomy'] ) ? (string) $new['taxonomy'] : self::TAXONOMY_TAG;
        $instance['taxonomy'] = $this->normalizeTaxonomy( $taxonomy );

        $instance['number']  = $this->clampInt( (int) ( $new['number'] ?? 20 ), 1, 100 );

        $orderby = isset( $new['orderby'] ) ? (string) $new['orderby'] : 'count';
        $instance['orderby'] = in_array( $orderby, [ 'count', 'name' ], true ) ? $orderby : 'count';

        $order = isset( $new['order'] ) ? strtoupper( (string) $new['order'] ) : 'DESC';
        $instance['order'] = in_array( $order, [ 'ASC', 'DESC' ], true ) ? $order : 'DESC';

        $this->flushCache();

        return $instance;
    }

    /* -----------------------------------------
     * Data providers (cached)
     * ----------------------------------------- */

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getCloudItems( string $taxonomy, int $number, string $orderby, string $order ): array {
        $cacheKey = $this->cacheKey( 'cloud', [ $taxonomy, $number, $orderby, $order ] );
        $cached = get_transient( $cacheKey );
        if ( is_array( $cached ) ) {
            return $cached;
        }

        // NOTE: No HTML in data. We return normalized arrays for Blade.
        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
            'number'     => $number,
            'orderby'    => $orderby,
            'order'      => $order,
        ] );

        $items = [];

        if ( ! is_wp_error( $terms ) && is_array( $terms ) ) {
            foreach ( $terms as $t ) {
                $items[] = [
                    'id'    => (int) $t->term_id,
                    'name'  => (string) $t->name,
                    'count' => (int) $t->count,
                    'link'  => (string) get_term_link( $t ),
                ];
            }
        }

        set_transient( $cacheKey, $items, static::CACHE_TTL );

        return $items;
    }

    private function normalizeTaxonomy( string $taxonomy ): string {
        $allowed = [ self::TAXONOMY_TAG, self::TAXONOMY_CATEGORY ];
        return in_array( $taxonomy, $allowed, true ) ? $taxonomy : self::TAXONOMY_TAG;
    }

    private function flushCache(): void {
        foreach ( [ self::TAXONOMY_TAG, self::TAXONOMY_CATEGORY ] as $tax ) {
            foreach ( [ 'count', 'name' ] as $orderby ) {
                foreach ( [ 'ASC', 'DESC' ] as $order ) {
                    foreach ( [ 10, 20, 30, 50, 100 ] as $n ) {
                        delete_transient( $this->cacheKey( 'cloud', [ $tax, $n, $orderby, $order ] ) );
                    }
                }
            }
        }
    }
}
