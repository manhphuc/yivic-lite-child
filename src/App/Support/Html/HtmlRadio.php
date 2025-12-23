<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlRadio
 *
 * Radio group renderer.
 *
 * Options:
 * - data (array<string, string>): value => label
 * - separator (string): string between radios (default: ' ')
 * - current_value (string|null): selected value
 * - item_attrs (array<string, mixed>): extra attrs applied to each <input>
 * - label_wrap (bool): wrap input+label inside <label> for better UX (default: true)
 */
final class HtmlRadio {
    /**
     * Create a group of <input type="radio">.
     *
     * @param string $name
     * @param string $value Ignored (kept for legacy signature). Use options[current_value].
     * @param array<string, mixed> $attrs Group-level attrs (optional, applied to each input)
     * @param array<string, mixed>|null $options
     * @return string
     */
    public static function create(
        string $name    = '',
        string $value   = '',
        array $attrs    = [],
        ?array $options = null
    ): string {
        $options = is_array( $options ) ? $options : [];

        $data = $options['data'] ?? [];
        if ( ! is_array( $data ) || $data === []) {
            return '';
        }

        $separator = isset( $options['separator'] ) ? (string) $options['separator'] : ' ';
        $current   = array_key_exists( 'current_value', $options ) ? (string) ( $options['current_value'] ?? '' ) : $value;

        $itemAttrs = $options['item_attrs'] ?? [];
        if ( ! is_array( $itemAttrs ) ) {
            $itemAttrs = [];
        }

        $labelWrap = !array_key_exists( 'label_wrap', $options ) || (bool) $options['label_wrap'];

        // Prevent overriding critical attrs coming from external
        unset( $attrs['type'], $attrs['name'], $attrs['value'], $itemAttrs['type'], $itemAttrs['name'], $itemAttrs['value'] );

        $chunks = [];

        foreach ( $data as $val => $label ) {
            $val                    = (string) $val;

            $inputAttrs             = array_merge( $attrs, $itemAttrs );
            $inputAttrs['name']     = $name;
            $inputAttrs['value']    = $val;

            if ( $val !== '' && $val === $current ) {
                $inputAttrs['checked'] = true; // boolean attribute
            } else {
                unset( $inputAttrs['checked'] );
            }

            $attrString = HtmlAttributes::build( $inputAttrs );
            $input      = sprintf( '<input type="radio"%s />', $attrString );

            $labelEsc   = HtmlAttributes::escape( (string) $label );

            if ( $labelWrap ) {
                $chunks[] = sprintf( '<label>%s %s</label>', $input, $labelEsc );
            } else {
                $chunks[] = $input . $labelEsc;
            }
        }

        return implode( $separator, $chunks );
    }
}
