<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlPassword
 *
 * HTML password input helper.
 *
 * Notes:
 * - By default, password fields should NOT be pre-filled for security reasons.
 * - If you do pass a value, it will be rendered (sometimes useful in controlled admin forms),
 *   but prefer leaving it empty.
 */
final class HtmlPassword {
    /**
     * Create an <input type="password"> element.
     *
     * Options:
     * - render_value (bool) If false, do not output the value attribute (default: false).
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
        unset( $attrs['type'], $attrs['name'] );

        $attrs['name'] = $name;

        $renderValue = (bool) ( $options['render_value'] ?? false );
        if ( $renderValue ) {
            unset( $attrs['value'] );
            $attrs['value'] = $value;
        } else {
            // Ensure we don't accidentally render old/stale values
            unset( $attrs['value'] );
        }

        $attrString = HtmlAttributes::build( $attrs );

        return sprintf( '<input type="password"%s />', $attrString );
    }
}
