<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Widget;

use WP_Widget;

/**
 * Class YivicWidgetBase
 *
 * A small base class to standardize WordPress widgets in a Laravel-style codebase.
 *
 * Goals:
 * - Keep widget() / form() / update() clean and consistent.
 * - Centralize tiny utilities (clamp, cache key, safe title).
 * - Encourage "data provider" approach: build data in PHP, render in Blade.
 *
 * How to use:
 * - Extend this class.
 * - Implement buildData() for front-end Blade rendering.
 * - Implement schema() + valuesFromInstance() for admin form rendering (schema-driven).
 */
abstract class YivicWidgetBase extends WP_Widget {
    /**
     * Widget cache namespace.
     * Override per widget to avoid collisions.
     */
    protected const CACHE_NS = 'yivic_widget';

    /**
     * Default cache TTL for widget datasets.
     */
    protected const CACHE_TTL = 300; // 5 minutes

    /**
     * Build a stable cache key for a widget.
     *
     * @param string $group Group name: featured|recent|comments...
     * @param array<int|string, string|int> $parts
     */
    protected function cacheKey( string $group, array $parts = [] ): string {
        $blogId = (int) get_current_blog_id();
        $base   = [
            static::CACHE_NS,
            (string) $blogId,
            $this->id_base ?: static::class,
            $group,
        ];

        foreach ( $parts as $p ) {
            $base[] = (string) $p;
        }

        return implode( '_', $base );
    }

    /**
     * Small helper for range-safe integer values.
     */
    protected function clampInt( int $value, int $min, int $max ): int {
        return max( $min, min( $max, $value ) );
    }

    /**
     * Resolve widget title: instance title or fallback.
     */
    protected function resolveTitle( array $instance, string $fallback ): string {
        $t = isset( $instance['title'] ) ? (string) $instance['title'] : '';
        $t = trim($t);

        return $t !== '' ? $t : $fallback;
    }

    /**
     * Subclasses should return schema for admin form.
     *
     * @return array<int, array<string, mixed>>
     */
    abstract protected function schema( array $values ): array;

    /**
     * Subclasses should map raw $instance to normalized values used by schema.
     *
     * @return array<string, mixed>
     */
    abstract protected function valuesFromInstance( array $instance ): array;

    /**
     * Subclasses build data for Blade view in front-end.
     *
     * @return array<string, mixed>
     */
    abstract protected function buildData( array $args, array $instance ): array;

    /**
     * Subclasses provide Blade view name.
     */
    abstract protected function view(): string;

    /**
     * Front-end output wrapper.
     */
    public function widget( $args, $instance ): void {
        $data = $this->buildData( (array) $args, (array) $instance );

        echo $args[ 'before_widget' ];

        // Blade render (your theme helper).
        echo theme_view( $this->view(), $data );

        echo $args[ 'after_widget' ];
    }

    /**
     * Admin form output via schema-driven renderer.
     */
    public function form( $instance ): void {
        $values = $this->valuesFromInstance( (array) $instance );
        $schema = $this->schema( $values );

        // Use your schema-driven form renderer.
        echo \Yivic\YivicLiteChild\App\Support\Form\YivicForm::renderWidgetForm( $this, $schema, $values );
    }
}

