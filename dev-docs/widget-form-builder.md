# Yivic Lite Child – Support Helpers Documentation

This document describes the **Support layer** used in Yivic Lite Child.
The goal is to provide a **clean, secure, reusable, and schema-driven** way
to build HTML, forms, and widgets without duplication or inline HTML noise.

All examples below are **framework-agnostic PHP**, compatible with WordPress,
and designed for long-term maintainability.

---

## Table of Contents

1. Philosophy & Design Goals
2. Directory Structure
3. HtmlAttributes (Core)
4. HTML Element Helpers
    - Textbox
    - Password
    - Hidden
    - Checkbox
    - Radio
    - Selectbox
    - Textarea
    - Button
    - File Upload
5. YivicHtml (Facade)
6. YivicForm (Schema-driven Form Builder)
7. Widget Integration (Best Practice)
8. Advanced Examples
9. Best Practices

---

## 1. Philosophy & Design Goals

- **Security first**: all attributes and values are escaped.
- **Zero inline HTML duplication**.
- **Schema-driven**: UI is described by data, not markup.
- **Composable**: small helpers, easy to extend.
- **No jQuery dependency**.
- **Widget-friendly** (WP_Widget compatible).

---

## 2. Directory Structure

```
src/App/Support
├── Form
│   └── YivicForm.php
├── Html
│   ├── HtmlAttributes.php
│   ├── HtmlTextbox.php
│   ├── HtmlPassword.php
│   ├── HtmlHidden.php
│   ├── HtmlCheckbox.php
│   ├── HtmlRadio.php
│   ├── HtmlSelectbox.php
│   ├── HtmlTextarea.php
│   ├── HtmlButton.php
│   ├── HtmlFileupload.php
│   └── YivicHtml.php
└── Widget
    └── YivicWidgetBase.php
```

---

## 3. HtmlAttributes (Core Utility)

Used internally by all HTML helpers.

### Example

```php
use Yivic\YivicLiteChild\App\Support\Html\HtmlAttributes;

echo HtmlAttributes::build([
    'class' => ['btn', 'btn-primary'],
    'data-id' => 12,
    'disabled' => false,
    'style' => [
        'width' => '100%',
        'margin-top' => '10px',
    ],
]);
```

**Output:**
```html
 class="btn btn-primary" data-id="12" style="width:100%;margin-top:10px"
```

---

## 4. HTML Element Helpers

All helpers return **HTML string**.

### 4.1 Textbox

```php
HtmlTextbox::render(
    name: 'username',
    value: 'john',
    attrs: ['class' => 'widefat', 'placeholder' => 'Username']
);
```

### 4.2 Password

```php
HtmlPassword::render(
    'password',
    '',
    ['class' => 'widefat']
);
```

### 4.3 Hidden

```php
HtmlHidden::render('token', 'abc123');
```

### 4.4 Checkbox (single)

```php
HtmlCheckbox::render(
    name: 'agree',
    value: '1',
    checked: true,
    attrs: ['id' => 'agree']
);
```

### 4.5 Checkbox (group)

```php
foreach (['php', 'js', 'css'] as $lang) {
    echo HtmlCheckbox::render(
        'skills[]',
        $lang,
        in_array($lang, ['php', 'js']),
        ['id' => 'skill_' . $lang]
    );
}
```

---

### 4.6 Radio Group

```php
HtmlRadio::render(
    name: 'gender',
    selected: 'female',
    options: [
        'male' => 'Male',
        'female' => 'Female',
    ],
    attrs: ['class' => 'radio']
);
```

---

### 4.7 Selectbox

```php
HtmlSelectbox::render(
    name: 'country',
    selected: 'vn',
    options: [
        'vn' => 'Vietnam',
        'us' => 'United States',
    ],
    attrs: ['class' => 'widefat']
);
```

---

### 4.8 Textarea

```php
HtmlTextarea::render(
    'bio',
    'Hello world',
    ['rows' => 4, 'class' => 'widefat']
);
```

---

### 4.9 Button

```php
HtmlButton::render(
    'submit',
    'Save',
    ['class' => 'button button-primary'],
    type: 'submit'
);
```

---

### 4.10 File Upload

```php
HtmlFileupload::render(
    'avatar',
    ['accept' => 'image/*']
);
```

---

## 5. YivicHtml (Facade)

Convenience wrapper.

```php
use Yivic\YivicLiteChild\App\Support\Html\YivicHtml;

$html = new YivicHtml();

echo $html->textbox('title', 'My Post', ['class' => 'widefat']);
echo $html->selectbox('status', 'draft', [
    'draft' => 'Draft',
    'publish' => 'Publish',
]);
```

---

## 6. YivicForm (Schema-driven Form Builder)

### Basic Concept

- Form is described by a **schema array**
- Rendering logic is centralized
- Widget `form()` becomes extremely short

---

### Example Schema

```php
$schema = [
    'title' => [
        'type' => 'text',
        'label' => 'Title',
        'default' => 'Widget Tabs',
        'attrs' => ['class' => 'widefat'],
    ],

    'count' => [
        'type' => 'number',
        'label' => 'Items count',
        'default' => 3,
        'attrs' => ['min' => 1, 'max' => 10],
    ],

    'source' => [
        'type' => 'select',
        'label' => 'Source',
        'options' => [
            'latest' => 'Latest',
            'category' => 'Category',
        ],
    ],
];
```

---

### Render in Widget `form()`

```php
use Yivic\YivicLiteChild\App\Support\Form\YivicForm;

public function form($instance): void {
    echo YivicForm::render(
        schema: self::FORM_SCHEMA,
        values: $instance,
        fieldId: fn($k) => $this->get_field_id($k),
        fieldName: fn($k) => $this->get_field_name($k),
    );
}
```

Result: **~15 lines only**.

---

## 7. Widget Integration Pattern

### Widget Base Class

```php
abstract class MyWidget extends YivicWidgetBase {
    protected const FORM_SCHEMA = [...];
}
```

### Benefits

- All widgets share same rendering logic
- Easy copy–paste to create new widgets
- No duplicated HTML

---

## 8. Advanced Examples

### Conditional Fields

```php
'source' => [
    'type' => 'select',
    'label' => 'Source',
    'options' => [...],
    'toggle' => [
        'category' => ['category_id'],
    ],
],

'category_id' => [
    'type' => 'select',
    'label' => 'Category',
    'options' => $categories,
],
```

---

### Repeatable Fields

```php
'items' => [
    'type' => 'repeat',
    'schema' => [
        'title' => ['type' => 'text'],
        'url' => ['type' => 'text'],
    ],
]
```

---

### Read-only / Disabled

```php
'api_key' => [
    'type' => 'text',
    'label' => 'API Key',
    'attrs' => ['readonly' => true],
]
```

---

## 9. Best Practices

- Prefer **schema-driven forms**
- Never echo raw HTML in widgets
- Keep helpers **stateless**
- Extend helpers instead of copying
- Keep Blade for frontend only

---

## Final Notes

This Support layer is designed to scale with:
- more widgets
- more admin screens
- future migration to SPA or headless UI

You should be able to create a new widget by:
1. Copying one widget class
2. Adjusting schema
3. Adjusting data provider

Everything else stays the same.
