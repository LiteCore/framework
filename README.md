# LiteCore

LiteCore is a lightweight PHP framework for building web applications.
It provides a minimal but complete foundation with a custom template
engine, database abstraction, session management, and a responsive
frontend built on jQuery, LESS, and HTML 5.

Originally developed as the core of
[LiteCart](https://www.litecart.net/), LiteCore can also be used as a
standalone framework for custom projects.

**Created by [T. Almroth](https://www.github.com/timint)**

**[Download](https://github.com/LiteCore/framework/archive/refs/heads/master.zip)**
| **[Website](https://litecore.dev/)**

## Features

- Lightweight PHP framework (no Composer dependencies)
- Custom template engine with snippet-based views
- MySQLi database wrapper with input escaping
- Session management with CSRF protection
- Responsive CSS framework (LESS-based) with grid, forms, buttons,
  cards, navbar, tables, and more
- JavaScript components: litebox (lightbox), carousel with touch/swipe,
  drag & drop, dropdowns, tabs, off-canvas, scroll-up, and more
- Virtual Modification system (vMod) for non-destructive file overrides
- Event system for extensibility
- Module system for pluggable functionality

## Requirements

- PHP 8.0 or higher (8.3+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Apache 2 with mod_rewrite

## Installation

1. Upload the contents of `public_html/` to your web root directory.

   Examples:

   - /var/www/html/
   - /home/username/public_html/
   - C:\xampp\htdocs\

2. Create a MySQL database and import `install/structure.sql`, then
   `install/data.sql`.

3. Copy `install/public_html/storage/config.inc.php` to your `storage/`
   folder and insert your database credentials.

If everything is set up correctly, LiteCore should load without errors.

## Build Tools

LiteCore uses Gulp 5 (ESM) for compiling LESS and minifying JavaScript.
Requires Node.js 20+.

   ```bash
   cd /path/to/project
   npm install
   npx gulp
   ```

## Frontend Components

The CSS framework is built with LESS and provides:

- Grid system with responsive breakpoints
- Form styling (inputs, checkboxes, selects, toggles)
- Buttons (default, success, danger, outline, transparent)
- Cards, badges, breadcrumbs, pagination
- Navbar, tabs, pills, dropdown menus
- Tables with sorting support
- Typography and spacing utilities
- Animations and transitions

JavaScript components (jQuery-based):

- Litebox (lightbox with gallery, keyboard nav, touch support)
- Carousel with swipe support
- Draggable elements
- Context menus
- Input tags, password visibility toggle
- Off-canvas sidebar
- Scroll-up button
- Momentum scroll

## Coding Standards

See [STANDARD.md](STANDARD.md) for syntax formatting and code standards.

See [NoNonsenseCoding.md](NoNonsenseCoding.md) for the project's coding
philosophy.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for how to contribute.

## Security

See [SECURITY.md](SECURITY.md) for reporting vulnerabilities.

## License

Licensed under [CC-BY-ND-4.0](LICENSE.md).
