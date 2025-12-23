<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlTextarea
 *
 * HTML textarea helper.
 *
 * Security:
 * - Attributes are escaped via HtmlAttributes.
 * - Text content is escaped to prevent HTML injection by default.
 *
 * Options:
 * - raw (bool): if true, do not escape content (default: false).
 */
final class HtmlTextarea {
    /**
     * Create a <textarea> element.
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
        unset( $attrs[ 'name' ] );

        $attrs['name'] = $name;

        $raw = (bool) ( $options['raw'] ?? false );
        $content = $raw ? $value : HtmlAttributes::escape( $value );

        $attrString = HtmlAttributes::build($attrs);

        return sprintf( '<textarea%s>%s</textarea>', $attrString, $content );
    }
}
