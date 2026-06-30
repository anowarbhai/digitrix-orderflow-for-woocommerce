# Digitrix OrderFlow for WooCommerce

Professional WooCommerce operations plugin for assigning orders to moderators, importing orders from remote WooCommerce stores, and giving moderators a focused order-processing interface.

## Plugin Metadata

- Plugin name: Digitrix OrderFlow for WooCommerce
- Text domain: `digitrix-orderflow-for-woocommerce`
- WordPress.org slug: `digitrix-orderflow-for-woocommerce`
- Requires WordPress: 5.8+
- Requires PHP: 7.4+
- Requires WooCommerce: 6.0+
- Current version: 1.0.0
- License: GPLv2 or later

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
| Recent Assignments | `moderator-recent-assignments` | Assigned order table, filters, and analytics |
| Sequence & Status | `moderator-sequence-status` | Moderator sequence, active/inactive state, shift controls |
| Product Assignments | `moderator-product-assignments` | Assign products to moderators |
| Reassign | `moderator-reassign-orders` | Bulk reassignment from selected source users to active users |
| Plugin Settings | `moderator-plugin-settings` | Role and plugin behavior settings |
| Remote Import | `moderator-remote-import` | Remote WooCommerce source configuration and manual import |
| My Orders | `digitrix-orderflow-my-orders` | Moderator-facing order list and mobile card workflow |

## File Structure

```text
digitrix-orderflow-for-woocommerce/
|-- digitrix-orderflow-for-woocommerce.php
|-- README.md
|-- readme.txt
|-- uninstall.php
|-- assets/
|   |-- css/
|   |   |-- admin.css
|   |   |-- recent-assignments.css
|   |   `-- moderator-orders.css
|   `-- js/
|       |-- admin-core.js
|       |-- recent-assignments.js
|       `-- moderator-orders.js
`-- includes/
    `-- pages/
        |-- dashboard.php
        |-- plugin-settings.php
        |-- product-assignments.php
        |-- recent-assignments.php
        |-- reassign-orders.php
        |-- remote-import.php
        `-- sequence-status.php
```

Each admin menu page has its own entry file in `includes/pages/`. The plugin still keeps some legacy rendering code in the main plugin file for compatibility, while the page files act as stable controllers.

## Remote Import Setup

1. Go to **Order Management -> Remote Import**.
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

- Today: previous day 10:00 PM to current day 10:00 PM
- Yesterday: previous business window
- This Month / Last Month: calendar month in the WordPress timezone
- Custom: selected start and end date

Order display dates are formatted in `Asia/Dhaka`.

## WordPress.org Submission Notes

- `readme.txt` is included for WordPress.org.
- External CDN dependencies have been removed.
- Generated ZIP files should not be committed or packaged.
- Remote API credentials should be created with the minimum required WooCommerce REST API permissions.
- Run the official Plugin Check plugin before final submission.
- Review any PHPCS warnings before packaging a release.

## Deployment

On the live server:

```bash
cd /path/to/wp-content/plugins/digitrix-orderflow-for-woocommerce
git pull origin main
```

Then clear any page cache and refresh the WordPress admin page.

## Development Notes

- Keep business logic prefixed with `aoam_`.
- Use WordPress/WooCommerce APIs where possible.
- Keep remote API credentials in WordPress options only.
- Run a syntax check before deploy:

```bash
php -l digitrix-orderflow-for-woocommerce.php
```

## Changelog

### 1.0.0

- Initial repository-ready release.
- Added moderator order assignment and product assignment controls.
- Added Recent Assignments AJAX filters and analytics.
- Added Moderator Orders mobile card workflow.
- Added remote WooCommerce REST API import and status sync.
- Added WordPress.org `readme.txt`, page assets, plugin action links, and uninstall guard.
