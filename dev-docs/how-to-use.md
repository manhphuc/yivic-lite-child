# How to Use — Yivic Lite Child

## Overview

Yivic Lite Child is built for developers who want a clean, modern WordPress theme architecture.

---

## Blade Templates

All Blade views live in:

resources/views/

Render example:

echo theme_view('widgets.widget_tabs', $data);

---

## Widget System

Widgets extend:

Yivic\YivicLiteChild\App\Support\Widget\YivicWidgetBase

Each widget:
- Builds data in PHP
- Renders markup via Blade
- Uses schema-driven admin forms

---

## Creating a Widget

Minimal example:

final class ExampleWidget extends YivicWidgetBase {

    protected function view(): string {
        return 'widgets.example';
    }

    protected function valuesFromInstance(array $instance): array {
        return [
            'title' => $instance['title'] ?? 'Example',
        ];
    }

    protected function schema(array $values): array {
        return [
            [
                'type'  => 'text',
                'name'  => 'title',
                'label' => 'Title',
            ],
        ];
    }

    protected function buildData(array $args, array $instance): array {
        return [
            'title' => $this->valuesFromInstance($instance)['title'],
        ];
    }
}

---

## HTML Helpers

Reusable helpers live in:

src/App/Support/Html/

They ensure consistent escaping and markup.

---

## Blade Cache

Safe to clear anytime:

rm -rf storage/framework/views/*

---

## License

GPLv3 or later  
https://www.gnu.org/licenses/gpl-3.0.html
