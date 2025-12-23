# Yivic Lite Child

**License:** GPLv3 or later  
**License URI:** https://www.gnu.org/licenses/gpl-3.0.html

---

## Description

**Yivic Lite Child** is the official child theme for **Yivic Lite**.

This theme is designed for developers who want to extend and customize Yivic Lite
without touching the parent theme code, ensuring safe updates and long-term maintainability.

Yivic Lite Child introduces a more structured, developer-friendly architecture:
- Dependency Injection (Illuminate Container–style)
- Blade-based view rendering
- Clean separation between logic and presentation
- Reusable UI helpers and widget infrastructure

It is ideal for building scalable features such as advanced widgets, UI components,
and maintainable templates on top of the Yivic Lite parent theme.

👉 Full developer documentation is available here:  
👉 **[Child Theme Documentation](./dev-docs/readme.md)**

---

## Features

- Official child theme for **Yivic Lite**
- Safe customization (no parent theme modification)
- Blade-based template rendering
- Structured, maintainable codebase
- Schema-driven widget admin forms
- Reusable HTML & form helper utilities
- WordPress-compatible, developer-friendly architecture
- Translation-ready (`yivic-lite-child` text domain)

---

## Requirements

- **WordPress:** 5.0+
- **PHP:** 7.4+
- **Parent Theme:** **Yivic Lite** (must be installed and activated)

---

## Installation

1. Install the parent theme **Yivic Lite** in:
   `/wp-content/themes/yivic-lite`
2. Download or clone this repository.
3. Upload the `yivic-lite-child` folder to:
   `/wp-content/themes/`
4. Activate **Yivic Lite Child** via **Appearance → Themes**.
5. (Optional) Review the developer documentation in `dev-docs/`.

---

## Documentation Structure

All developer documentation for **Yivic Lite Child** lives inside the **`dev-docs/`** directory.

### Available documents

- **Overview & Architecture**
    - `dev-docs/readme.md`

- **Installation Guide**
    - `dev-docs/installation.md`

- **How to Use the Child Theme**
    - `dev-docs/how-to-use.md`

- **Widget Form Builder & HTML Helpers**
    - `dev-docs/widget-form-builder.md`

---

## Development Notes

- This child theme assumes **Yivic Lite** as the parent.
- Compiled Blade cache and runtime files (e.g. `storage/framework/views`) should **not** be committed.
- All custom development should live in the child theme, not the parent.

---

## License

Yivic Lite Child is licensed under the **GPLv3 or later**.

You are free to use, modify, and distribute this theme under the same license.

See the full license at:  
https://www.gnu.org/licenses/gpl-3.0.html
