<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlSelectbox
 *
 * Select box helper.
 *
 * Options:
 * - data (array<string, string>): value => label
 * - multiple (bool): render multiple select, and accept $value as array
 * - placeholder (string): if provided, add a first <option value="">...</option>
 */
final class HtmlSelectbox {
    /**
     * Create a <select> element.
     *
     * @param string $name
     * @param string $value
     * @param array<string, mixed> $attrs
     * @param array<string, mixed>|null $options
     * @return string
     */
    public static function create(
        string $name    = '',
        string $value   = '',
        array  $attrs   = [],
        ?array $options = null
    ): string {
        $options = is_array( $options ) ? $options : [];

        $data = $options['data'] ?? [];
        if ( ! is_array( $data ) ) {
            $data = [];
        }

        $multiple = ! empty( $options['multiple'] );

        unset( $attrs['name'] );
        $attrs['name'] = $name;

        if ( $multiple ) {
            $attrs['multiple'] = true;
        }

        // Normalize selected values
        $selectedValues = [];
        if ( $multiple ) {
            if ( is_array( $value ) ) {
                foreach  ( $value as $v ) {
                    $selectedValues[ (string) $v ] = true;
                }
            }
        } else {
            $selectedValues[ (string) $value ] = true;
        }

        $optionsHtml = [];

        if ( isset( $options['placeholder'] ) && is_string( $options['placeholder'] ) && $options['placeholder'] !== '' ) {
            $optionsHtml[] = sprintf(
                '<option value="">%s</option>',
                HtmlAttributes::escape( $options['placeholder'] )
            );
        }

        foreach ( $data as $val => $label ) {
            $valStr = (string) $val;

            $optAttrs = [ 'value' => $valStr ];
            if ( isset( $selectedValues[$valStr] ) ) {
                $optAttrs['selected'] = true;
            }

            $optionsHtml[] = sprintf(
                '<option%s>%s</option>',
                HtmlAttributes::build( $optAttrs ),
                HtmlAttributes::escape( (string) $label )
            );
        }

        $attrString = HtmlAttributes::build( $attrs );

        return sprintf(
            '<select%s>%s</select>',
            $attrString,
            implode( '', $optionsHtml )
        );
    }
}
