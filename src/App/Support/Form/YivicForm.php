<?php
declare(strict_types=1);

namespace Yivic\YivicLiteChild\App\Support\Form;

use WP_Widget;
use Yivic\YivicLiteChild\App\Support\Html\YivicHtml;

/**
 * Class YivicForm
 *
 * Schema-driven form renderer for WordPress Widget admin forms (no jQuery).
 *
 * Why:
 * - Keep WP_Widget::form() tiny (schema + values only).
 * - Reuse across multiple widgets.
 * - Centralize sanitization/escaping responsibilities (via HtmlAttributes/YivicHtml).
 *
 * Schema format (per field):
 * [
 *   'type'  => 'text'|'number'|'select'|'textarea'|'checkbox'|'hidden'|'password'|'file',
 *   'key'   => 'title',              // widget instance key
 *   'label' => 'Title',              // optional
 *   'attrs' => ['class' => 'widefat'],// optional (merged into field attrs)
 *   'options' => ['a'=>'A'],         // for select or radioGroup
 *   'empty' => ['enabled'=>true,'label'=>'— Select —'], // for select
 *   'when' => ['featured_source' => 'category'], // conditional rendering based on current values
 *   'help' => 'Small hint under field' // optional
 * ]
 */
final class YivicForm
{
    /**
     * Render a widget form based on schema.
     *
     * @param WP_Widget $widget
     * @param array<int, array<string, mixed>> $schema
     * @param array<string, mixed> $values Current widget instance values
     * @return string
     */
    public static function renderWidgetForm(WP_Widget $widget, array $schema, array $values): string
    {
        $out = '';

        foreach ($schema as $field) {
            if (!is_array($field) || empty($field['type']) || empty($field['key'])) {
                continue;
            }

            // Conditional display (server-side).
            if (!self::passesWhen($field, $values)) {
                continue;
            }

            $type  = (string) $field['type'];
            $key   = (string) $field['key'];
            $label = isset($field['label']) ? (string) $field['label'] : '';

            $id   = $widget->get_field_id($key);
            $name = $widget->get_field_name($key);

            $value = $values[$key] ?? null;

            // Base attributes for common WP widget styles.
            $attrs = is_array($field['attrs'] ?? null) ? (array) $field['attrs'] : [];
            $attrs = array_replace(['id' => $id], $attrs);

            $control = '';

            switch ($type) {
                case 'text':
                    $attrs = array_replace(['class' => 'widefat', 'type' => 'text'], $attrs);
                    $control = YivicHtml::input('text', $name, (string) $value, $attrs);
                    break;

                case 'number':
                    $attrs = array_replace(['class' => 'tiny-text', 'type' => 'number', 'step' => 1], $attrs);
                    $control = YivicHtml::input('number', $name, (string) $value, $attrs);
                    break;

                case 'hidden':
                    $control = YivicHtml::input('hidden', $name, (string) $value, $attrs);
                    // hidden fields typically don't need label wrapper
                    $out .= $control;
                    continue 2;

                case 'password':
                    $attrs = array_replace(['class' => 'widefat', 'type' => 'password'], $attrs);
                    $control = YivicHtml::input('password', $name, (string) $value, $attrs);
                    break;

                case 'file':
                    $attrs = array_replace(['class' => 'widefat', 'type' => 'file'], $attrs);
                    $control = YivicHtml::input('file', $name, null, $attrs);
                    break;

                case 'textarea':
                    $attrs = array_replace(['class' => 'widefat', 'rows' => 4], $attrs);
                    $control = YivicHtml::textarea($name, (string) $value, $attrs);
                    break;

                case 'checkbox':
                    // Checkbox in widgets is usually one field => bool.
                    // If you want "checked" to represent truthy values, pass 'truthy' => '1' etc.
                    $truthy  = isset($field['truthy']) ? (string) $field['truthy'] : '1';
                    $checked = self::truthyEquals($value, $truthy);

                    // Force id for label "for".
                    $control = YivicHtml::checkbox($name, $truthy, $checked, $attrs);

                    // Checkbox label often goes inline to the right
                    $labelInline = $label !== '' ? ' ' . $label : '';
                    $out .= YivicHtml::p(
                        '<label for="' . $id . '">' . $control . $labelInline . '</label>'
                        . self::help($field)
                    );
                    continue 2;

                case 'select':
                    $options = is_array($field['options'] ?? null) ? (array) $field['options'] : [];

                    $emptyEnabled = (bool) (($field['empty']['enabled'] ?? false));
                    $emptyLabel   = (string) (($field['empty']['label'] ?? '— Select —'));

                    $attrs = array_replace(['class' => 'widefat'], $attrs);
                    $control = YivicHtml::select($name, is_scalar($value) ? (string) $value : null, $options, $attrs, $emptyEnabled, $emptyLabel);
                    break;

                default:
                    // Unknown type: skip safely.
                    continue 2;
            }

            // Default: label on top + control below.
            $block = '';
            if ($label !== '') {
                $block .= YivicHtml::label($label, ['for' => $id]) . '<br />';
            }
            $block .= $control . self::help($field);

            $out .= YivicHtml::p($block);
        }

        return $out;
    }

    /**
     * Evaluate conditional "when".
     *
     * Supported:
     * - ['when' => ['featured_source' => 'category']]
     * - multiple conditions ANDed.
     *
     * @param array<string, mixed> $field
     * @param array<string, mixed> $values
     */
    private static function passesWhen(array $field, array $values): bool
    {
        if (!isset($field['when']) || !is_array($field['when'])) {
            return true;
        }

        foreach ($field['when'] as $k => $expected) {
            $actual = $values[(string) $k] ?? null;

            if ((string) $actual !== (string) $expected) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if a field value matches a given truthy value.
     */
    private static function truthyEquals(mixed $value, string $truthy): bool
    {
        if (is_bool($value)) {
            return $value === true;
        }

        if (is_numeric($value) || is_string($value)) {
            return (string) $value === $truthy;
        }

        return false;
    }

    /**
     * Optional help text below control.
     *
     * @param array<string, mixed> $field
     */
    private static function help(array $field): string
    {
        $help = isset($field['help']) ? trim((string) $field['help']) : '';
        if ($help === '') {
            return '';
        }

        // WP style: <small class="description">...</small>
        return '<br /><small class="description">' . \Yivic\YivicLiteChild\App\Support\Html\HtmlAttributes::escape($help) . '</small>';
    }
}

