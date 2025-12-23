<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlCheckbox
 *
 * HTML checkbox helper.
 *
 * Design goals:
 * - Security: escape all attributes and values.
 * - Predictability: explicit handling of "checked" state.
 * - Reusability: consistent API with other Html* helpers.
 * - Framework-ready: no jQuery, no inline JS, no WordPress-specific functions.
 *
 * Notes:
 * - "checked" is controlled via $options['checked'] OR $options['current_value'].
 * - Attribute bag MUST NOT contain: type, value, name, checked (they are managed internally).
 */
final class HtmlCheckbox {
    /**
     * Create an <input type="checkbox"> element.
     *
     * Example:
     * HtmlCheckbox::create(
     *     'accept_terms',
     *     '1',
     *     ['class' => 'widefat'],
     *     ['checked' => true]
     * );
     *
     * @param string $name     Input name attribute.
     * @param string $value    Checkbox value.
     * @param array<string, mixed> $attrs   Additional HTML attributes.
     * @param array<string, mixed>|null $options
     *        Supported options:
     *        - checked (bool)          Explicit checked state
     *        - current_value (mixed)   Compare with $value to determine checked state
     */
    public static function create(
        string $name    = '',
        string $value   = '1',
        array $attrs    = [],
        ?array $options = null
    ): string {
        $isChecked = self::resolveCheckedState( $value, $options );

        // Prevent collisions with internally controlled attributes
        unset(
            $attrs['type'],
            $attrs['name'],
            $attrs['value'],
            $attrs['checked']
        );

        $attrs['name']  = $name;
        $attrs['value'] = $value;

        if ($isChecked) {
            // Boolean attributes are handled by HtmlAttributes
            $attrs['checked'] = true;
        }

        $attrString = HtmlAttributes::build( $attrs );

        return sprintf(
            '<input type="checkbox"%s />',
            $attrString
        );
    }

    /**
     * Resolve checked state from options.
     *
     * Priority:
     * 1. options['checked'] (explicit boolean)
     * 2. options['current_value'] === $value
     */
    private static function resolveCheckedState( string $value, ?array $options ): bool {
        if ( $options === null ) {
            return false;
        }

        if ( array_key_exists( 'checked', $options ) ) {
            return (bool) $options['checked'];
        }

        if ( array_key_exists( 'current_value', $options ) ) {
            return (string) $options['current_value'] === $value;
        }

        return false;
    }
}
