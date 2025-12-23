<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlFileupload
 *
 * HTML file input helper.
 *
 * Design goals:
 * - Security: escape all attributes via HtmlAttributes.
 * - Correctness: do NOT output a "value" attribute for file inputs.
 * - Reusability: consistent signature with other Html* helpers.
 * - Extensibility: support options like accept/multiple/capture easily.
 *
 * Notes:
 * - For security reasons, browsers ignore or block setting "value" for <input type="file">.
 * - If you want to show an existing file, render a separate preview/link outside.
 */
final class HtmlFileupload {
    /**
     * Create an <input type="file"> element.
     *
     * Example:
     * HtmlFileupload::create(
     *     'avatar',
     *     '', // ignored for file input
     *     ['class' => 'widefat', 'accept' => 'image/*'],
     *     ['multiple' => false]
     * );
     *
     * @param string $name   Input name attribute.
     * @param string $value  Ignored for file input (kept for API compatibility).
     * @param array<string, mixed> $attrs Additional HTML attributes.
     * @param array<string, mixed>|null $options
     *        Supported options:
     *        - multiple (bool)  Add "multiple" boolean attribute
     *        - accept (string)  Set accept attribute (e.g. "image/*")
     *        - capture (string|bool) Add capture attribute (mobile camera)
     */
    public static function create(
        string $name    = '',
        string $value   = '',
        array $attrs    = [],
        ?array $options = null
    ): string {
        // Prevent collisions with internally controlled attributes
        unset(
            $attrs['type'],
            $attrs['name'],
            $attrs['value']
        );

        $attrs['name'] = $name;

        // Apply options in a predictable way
        if ( is_array( $options ) ) {
            if ( array_key_exists( 'accept', $options ) && is_string( $options['accept'] ) && $options['accept'] !== '' ) {
                $attrs['accept'] = $options['accept'];
            }

            if ( ! empty( $options['multiple'] ) ) {
                $attrs['multiple'] = true; // boolean attribute
            }

            if ( array_key_exists( 'capture', $options ) ) {
                // capture can be true (-> "capture") or a string like "environment" / "user"
                $attrs['capture'] = $options['capture'] === true ? true : (string) $options['capture'];
            }
        }

        $attrString = HtmlAttributes::build( $attrs );

        return sprintf(
            '<input type="file"%s />',
            $attrString
        );
    }
}
