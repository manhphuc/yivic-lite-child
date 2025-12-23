<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlTextbox
 *
 * HTML text input helper.
 */
final class HtmlTextbox {
    /**
     * Create an <input type="text"> element.
     *
     * Options:
     * - type (string) Override input type (e.g. "email", "number", "url") default: "text"
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
        array $attrs    = [],
        ?array $options = null
    ): string {
        $type = is_array( $options ) && isset( $options['type'] ) && is_string( $options['type'] ) && $options['type'] !== ''
            ? $options['type']
            : 'text';

        unset( $attrs['type'], $attrs['name'], $attrs['value'] );

        $attrs['name']  = $name;
        $attrs['value'] = $value;

        $attrString = HtmlAttributes::build( $attrs );

        return sprintf( '<input type="%s"%s />', HtmlAttributes::escape( $type ), $attrString );
    }
}
