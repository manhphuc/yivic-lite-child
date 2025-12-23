<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class YivicHtml
 *
 * Small HTML UI helpers built for WordPress admin (widgets/forms) but framework-friendly.
 *
 * Design goals:
 * - Security-first: escape text + attributes by default.
 * - Maintainable: one place to build consistent markup.
 * - Extensible: used by schema-driven builders (YivicForm).
 *
 * Notes:
 * - This class returns HTML strings. You decide where to echo.
 * - For admin widget forms, WordPress expects <p> wrappers (same as core widgets).
 */
final class YivicHtml {
    /**
     * Wrap content in a <p> tag (WP widget form convention).
     *
     * @param string $content Inner HTML (assumed safe or already escaped).
     * @param array<string, mixed> $attrs
     */
    public static function p( string $content, array $attrs = [] ): string {
        return '<p' . HtmlAttributes::build( $attrs ) . '>' . $content . '</p>';
    }

    /**
     * Render a <label> element.
     *
     * @param string $text Human label text (escaped).
     * @param array<string, mixed> $attrs
     */
    public static function label( string $text, array $attrs = [] ): string {
        return '<label' . HtmlAttributes::build( $attrs ) . '>' . HtmlAttributes::escape( $text ) . '</label>';
    }

    /**
     * Generic input.
     *
     * @param string $type input type: text|number|hidden|password|file|...
     * @param string $name input name
     * @param string|int|float|null $value
     * @param array<string, mixed> $attrs Extra attributes: id, class, placeholder, min, max...
     * @return string
     */
    public static function input( string $type, string $name, string|int|float|null $value = null, array $attrs = [] ): string {
        $base = [
            'type' => $type,
            'name' => $name,
        ];

        // Allow explicit value="" when needed.
        if ( $value !== null ) {
            $base['value'] = (string) $value;
        }

        // Merge but keep caller's attrs authoritative.
        $attrs = array_replace( $base, $attrs );

        return '<input' . HtmlAttributes::build( $attrs ) . ' />';
    }

    /**
     * Textarea.
     *
     * @param string $name textarea name
     * @param string|null $value textarea content
     * @param array<string, mixed> $attrs
     */
    public static function textarea( string $name, ?string $value = null, array $attrs = [] ): string {
        $attrs = array_replace( [ 'name' => $name ], $attrs );

        return '<textarea' . HtmlAttributes::build( $attrs ) . '>' . HtmlAttributes::escape( (string) $value ) . '</textarea>';
    }

    /**
     * Select box.
     *
     * @param string $name
     * @param string|int|null $selected
     * @param array<string, string|int> $options value => label
     * @param array<string, mixed> $attrs
     * @param bool $includeEmpty Whether to include an empty option.
     * @param string $emptyLabel Label for empty option.
     * @return string
     */
    public static function select(
        string $name,
        string|int|null $selected,
        array $options,
        array $attrs = [],
        bool $includeEmpty = false,
        string $emptyLabel = '— Select —'
    ): string {
        $attrs = array_replace( [ 'name' => $name ], $attrs );

        $htmlOptions = '';

        if ( $includeEmpty ) {
            $htmlOptions .= self::option( '0', $emptyLabel, (string) $selected === '0' );
        }

        foreach ( $options as $value => $label ) {
            $isSelected = ( (string) $selected !== '' && (string) $selected === (string) $value );
            $htmlOptions .= self::option( (string) $value, (string) $label, $isSelected );
        }

        return '<select' . HtmlAttributes::build($attrs) . '>' . $htmlOptions . '</select>';
    }

    /**
     * Single checkbox (with checked state).
     *
     * @param string $name
     * @param string|int $value
     * @param bool $checked
     * @param array<string, mixed> $attrs
     * @return string
     */
    public static function checkbox( string $name, string|int $value, bool $checked = false, array $attrs = [] ): string {
        $attrs = array_replace(
            [
                'type'  => 'checkbox',
                'name'  => $name,
                'value' => (string) $value,
            ],
            $attrs
        );

        if ( $checked ) {
            $attrs['checked'] = true; // HtmlAttributes::build => checked="checked"
        }

        return '<input' . HtmlAttributes::build($attrs) . ' />';
    }

    /**
     * A simple radio group: options as value => label.
     *
     * @param string $name
     * @param string|int|null $selected
     * @param array<string, string|int> $options
     * @param string $separator
     * @param array<string, mixed> $inputAttrs
     * @return string
     */
    public static function radioGroup(
        string $name,
        string|int|null $selected,
        array $options,
        string $separator = ' ',
        array $inputAttrs = []
    ): string {
        $out = '';

        foreach ( $options as $value => $label ) {
            $isChecked = ( (string) $selected !== '' && (string) $selected === (string) $value );

            $attrs = array_replace(
                [
                    'type'  => 'radio',
                    'name'  => $name,
                    'value' => (string) $value,
                ],
                $inputAttrs
            );

            if ( $isChecked ) {
                $attrs['checked'] = true;
            }

            $out .= '<label style="margin-right:8px;">'
                . '<input' . HtmlAttributes::build( $attrs ) . ' /> '
                . HtmlAttributes::escape( (string) $label )
                . '</label>'
                . $separator;
        }

        return $out;
    }

    /**
     * Internal option helper.
     */
    private static function option(string $value, string $label, bool $selected): string {
        $attrs = ['value' => $value];

        if ( $selected ) {
            $attrs['selected'] = true; // selected="selected"
        }

        return '<option' . HtmlAttributes::build( $attrs ) . '>' . HtmlAttributes::escape( $label ) . '</option>';
    }
}

