# Installation — Yivic Lite Child

## Overview

**Yivic Lite Child** is a developer-focused child theme built on top of **Yivic Lite**.

It extends the parent theme using:
- Blade templates
- Dependency Injection (Illuminate Container)
- Schema-driven widget forms
- Clean, reusable widget architecture

⚠️ The parent theme **Yivic Lite** is required.

---

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Parent Theme: Yivic Lite
- Child Theme Folder: yivic-lite-child

---

## Step 1 — Install Parent Theme

Place the parent theme at:

wp-content/themes/yivic-lite

Activate it once via WordPress Admin.

---

## Step 2 — Install Child Theme

Upload or clone this repository into:

wp-content/themes/yivic-lite-child

---

## Step 3 — Activate Child Theme

Go to:
Appearance → Themes  
Activate **Yivic Lite Child**

---

## Blade Cache

Compiled Blade views are stored in:

yivic-lite-child/storage/framework/views

These files are runtime-only and should not be committed.

Recommended .gitignore:

/storage/*
!/storage/.gitignore

---

## License

GPLv3 or later  
https://www.gnu.org/licenses/gpl-3.0.html
