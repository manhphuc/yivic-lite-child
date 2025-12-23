<?php
declare( strict_types =1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlHidden
 *
 * HTML hidden input helper.
 *
 * Security:
 * - Escapes attribute values via HtmlAttributes.
 * - Prevents overriding critical attributes (type, name, value) from $attrs.
 */
final class HtmlHidden {
    /**
     * Create an <input type="hidden"> element.
     *
     * @param string $name
     * @param string $value
     * @param array<string, mixed> $attrs
     * @param array<string, mixed>|null $options (reserved for future)
     * @return string
     */
    public static function create(
        string $name    = '',
        string $value   = '',
        array $attrs    = [],
        ?array $options = null
    ): string {
        unset( $attrs['type'], $attrs['name'], $attrs['value'] );

        $attrs['name']  = $name;
        $attrs['value'] = $value;

        $attrString = HtmlAttributes::build( $attrs );

        return sprintf( '<input type="hidden"%s />', $attrString );
    }
}
