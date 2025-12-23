# Yivic Lite Child — Documentation

## Overview

**Yivic Lite Child** is the official child theme for **Yivic Lite**.

This child theme is designed for developers who want a clean, structured, and extensible architecture while staying fully compatible with WordPress core.

It introduces a modern development approach:
- Dependency Injection (Illuminate Container)
- Blade-based view rendering
- Schema-driven widget admin forms
- Reusable HTML & form helpers
- Clean separation between logic and presentation

---

## Architecture Goals

- Do NOT modify the parent theme directly
- Keep logic in PHP, markup in Blade
- Avoid duplicated widget/form code
- Make widgets easy to extend and reuse
- Stay WordPress-compatible (no framework lock-in)

---

## Documentation Index

- **Installation** → `installation.md`
- **How to Use** → `how-to-use.md`
- **Widget Form Builder** → `widget-form-builder.md`

---

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Parent Theme: **Yivic Lite**

---

## License

GPLv3 or later  
https://www.gnu.org/licenses/gpl-3.0.html
