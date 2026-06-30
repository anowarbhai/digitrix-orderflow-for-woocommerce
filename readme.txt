=== Digitrix OrderFlow for WooCommerce ===
Contributors: digitrixlabs
Tags: woocommerce, orders, order management, workflow, rest api
Requires at least: 5.8
Tested up to: 7.0
Requires PHP: 7.4
Requires Plugins: woocommerce
WC requires at least: 6.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Order assignment, moderator workflow, and remote WooCommerce order import for operations teams.

== Description ==

Digitrix OrderFlow for WooCommerce helps store teams distribute WooCommerce orders to moderators based on status, product assignment, user sequence, and availability.

The plugin includes an admin order management area, a moderator-facing My Orders page, Recent Assignments reporting, remote WooCommerce REST API import, and status sync for imported orders.

= Key Features =

* Automatic WooCommerce order assignment by sequence and status.
* Product-specific moderator assignments.
* Moderator active/inactive and shift controls.
* Moderator-only My Orders page with mobile card layout.
* AJAX filtering for Recent Assignments and moderator orders.
* Completion and cancellation flow analytics.
* Remote WooCommerce REST API import from multiple source stores.
* Status sync from imported local orders back to remote WooCommerce source stores.
* Asia/Dhaka operational date display and business-day filters.

= Privacy and Remote API Notes =

Remote import requires a WooCommerce REST API consumer key and consumer secret from each connected source store. These credentials are stored in this WordPress site's database options and are only available to administrators with plugin settings access.

Use HTTPS for remote source URLs. Do not use public or shared API credentials.

When a local imported order status is changed, the plugin can use the saved remote source credentials to update the matching order status on the source store.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin from the WordPress Plugins screen.
3. Make sure WooCommerce is installed and active.
4. Go to Order Management and configure users, sequences, products, and settings.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =

Yes. WooCommerce must be installed and active.

= Can orders be imported from more than one WooCommerce site? =

Yes. The Remote Import page supports multiple WooCommerce REST API sources.

= Does the plugin delete data when uninstalled? =

No. Operational order assignment history is preserved by design.

= Why should I use a server cron? =

Remote imports and delayed assignments rely on WP-Cron. Production sites should configure a real server cron to call WordPress cron regularly.

= What permissions do the remote API keys need? =

Use WooCommerce REST API credentials with the minimum permissions required to read orders and update order status on the connected source store.

== Screenshots ==

1. Order Management dashboard.
2. Recent Assignments filters and analytics.
3. Moderator My Orders mobile card view.
4. Remote WooCommerce API Sources.

== Changelog ==

= 1.0.0 =
* Initial release with moderator order assignment, product assignments, Recent Assignments analytics, moderator My Orders workflow, remote WooCommerce REST API import, and status sync.
