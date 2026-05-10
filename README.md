# WooCommerce Order Auto Assign To Moderator

Professional WooCommerce operations plugin for assigning orders to moderators, importing orders from remote WooCommerce stores, and giving moderators a focused order-processing interface.

## Plugin Metadata

- **Plugin name:** WooCommerce Order Auto Assign To Moderator
- **Text domain:** `auto-order-assign-moderator`
- **Requires WordPress:** 5.8+
- **Requires PHP:** 7.4+
- **Requires WooCommerce:** 6.0+
- **Current version:** 1.2.0
- **License:** GPL v2 or later

## Core Features

- Automatic order assignment by product, status, user sequence, and active shift.
- Separate moderator order dashboard with mobile card view.
- AJAX filters for moderator order lists and recent assignments.
- Recent Assignments dashboard with search, source, status, date filters, and flow analytics.
- Completion and cancellation flow chart for Processing/Partial orders.
- Manual status and assigned moderator updates via modal/AJAX.
- Remote WooCommerce REST API import from multiple source sites.
- Remote order status sync back to source stores.
- WP-Cron support with an admin fallback for remote imports.
- Dhaka timezone display for operational order dates.

## Admin Pages

| Page | Slug | Purpose |
| --- | --- | --- |
| Dashboard | `moderator-settings` | User counts, assignment overview, system summary |
| Recent Assignments | `moderator-recent-assignments` | Assigned order table, filters, analytics, export tools |
| Sequence & Status | `moderator-sequence-status` | Moderator sequence, active/inactive state, shift controls |
| Product Assignments | `moderator-product-assignments` | Assign products to moderators |
| Reassign | `moderator-reassign-orders` | Bulk reassignment from inactive users |
| Plugin Settings | `moderator-plugin-settings` | Role and plugin behavior settings |
| Remote Import | `moderator-remote-import` | Remote WooCommerce source configuration and manual import |
| My Orders | `moderator-simple-orders` | Moderator-facing order list and mobile card workflow |

## File Structure

```text
auto-order-assign-to-moderator/
├── auto-order-assign-to-moderator.php
├── README.md
├── uninstall.php
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   ├── recent-assignments.css
│   │   └── simple-orders.css
│   └── js/
│       ├── admin-core.js
│       ├── admin.js
│       ├── recent-assignments.js
│       └── simple-orders.js
└── includes/
    ├── class-admin-pages.php
    ├── class-moderator-interface.php
    ├── class-order-assignment.php
    └── functions.php
```

The plugin currently keeps legacy page rendering in the main plugin file for compatibility. Shared and page-specific CSS/JS are loaded from `assets/` so future page extraction can be done safely without changing public behavior.

## Remote Import Setup

1. Go to **Order Management → Remote Import**.
2. Enable remote import.
3. Add each remote WooCommerce site URL.
4. Add WooCommerce REST API consumer key and secret.
5. Select statuses to import, such as `processing`, `partial`, or `pending`.
6. Save settings.
7. Use **Run Import Now** to test manually.
8. Configure server cron for production.

Recommended server cron:

```bash
*/5 * * * * cd /home/USERNAME/public_html && /usr/local/bin/php wp-cron.php >/dev/null 2>&1
```

If `wp-cron.php` over HTTPS returns `403 Forbidden`, use PHP CLI cron instead of `wget` or `curl`.

## Date Rules

Recent Assignments date filters use the business-day rule requested for operations:

- **Today:** previous day 10:00 PM to current day 10:00 PM
- **Yesterday:** previous business window
- **This Month / Last Month:** calendar month in the WordPress timezone
- **Custom:** selected start and end date

Order display dates are formatted in `Asia/Dhaka`.

## Deployment

On the live server:

```bash
cd /path/to/wp-content/plugins/auto-order-assign-to-moderator
git pull origin main
```

Then clear any page cache and refresh the WordPress admin page.

## Development Notes

- Keep business logic prefixed with `aoam_`.
- Use WordPress/WooCommerce APIs where possible.
- Keep remote API credentials in WordPress options only.
- Run a syntax check before deploy:

```bash
php -l auto-order-assign-to-moderator.php
```

## Changelog

### 1.2.0

- Added centralized plugin constants and page-specific asset loading.
- Added professional README and deployment guidance.
- Added page-specific CSS/JS asset files for Recent Assignments and Simple Orders.
- Added plugin action links and uninstall guard.

### 1.1.x

- Added remote WooCommerce REST API import and status sync.
- Added AJAX Recent Assignments and Simple Orders workflows.
- Added mobile card view for moderator orders.
- Added completion/cancellation flow analytics.
- Added Dhaka timezone display.
