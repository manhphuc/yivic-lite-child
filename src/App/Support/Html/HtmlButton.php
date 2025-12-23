<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlButton
 *
 * Button helper focused on producing safe, predictable markup.
 *
 * Design notes:
 * - Uses <input> for compatibility with old WP widget forms and simple submit/reset buttons.
 * - Supports type: button|submit|reset (default: submit).
 * - Centralizes escaping via HtmlAttributes.
 * - Avoids mixing "type" and "value" in the attribute bag to prevent duplication.
 */
final class HtmlButton {
    private const ALLOWED_TYPES = ['button', 'submit', 'reset'];

    /**
     * Create an <input type="..."> button.
     *
     * Example:
     * HtmlButton::create('save', 'Save', ['class' => 'button button-primary'], ['type' => 'submit']);
     *
     * @param string $name    Input name attribute.
     * @param string $label   Value/label shown on the button.
     * @param array<string, mixed> $attrs   Additional HTML attributes (id, class, data-*, style...).
     * @param array<string, mixed> $options Supported: ['type' => 'submit'|'button'|'reset'].
     */
    public static function create(
        string $name    = '',
        string $label   = '',
        array $attrs    = [],
        ?array $options = null
    ): string {
        $type = self::normalizeType( $options['type'] ?? 'submit' );

        // Prevent collisions: we control these keys explicitly.
        unset( $attrs['type'], $attrs['value'], $attrs['name'] );

        $attrs['name']  = $name;
        $attrs['value'] = $label;

        $attrString = HtmlAttributes::build( $attrs );

        return sprintf(
            '<input type="%s"%s />',
            htmlspecialchars( $type, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ),
            $attrString
        );
    }

    private static function normalizeType(string $type): string {
        $type = strtolower( trim( $type ) );
        return in_array( $type, self::ALLOWED_TYPES, true ) ? $type : 'submit';
    }
}
