# Auto Order Assign To Moderator

**Contributors:** Anowar Hossain  
**Company:** Shirin Fashion  
**Plugin URL:** https://shirinshoes.com/  
**Requires at least:** WordPress 5.8  
**Tested up to:** WordPress 6.3  
**Requires PHP:** 7.4  
**WC requires at least:** 6.0  
**License:** GPL v2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

## Description

Automatically assign WooCommerce orders to moderators based on product specialization and round-robin sequencing. This plugin ensures fair distribution of orders among moderators while considering their product expertise.

## Features

- **Product-Based Assignment**: Assign specific products to moderators
- **Round-Robin Sequencing**: Fair distribution using sequence numbers
- **Moderator Management**: Comprehensive admin interface for managing moderators
- **Order Filtering**: Moderators only see orders assigned to them
- **Real-time Assignment**: Automatic assignment when new orders are created
- **Bulk Operations**: Manage multiple moderators and products efficiently
- **Email Notifications**: Notify moderators when assigned new orders

## Installation

1. Upload the `auto-order-assign-to-moderator` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure WooCommerce is installed and active
4. Go to Moderator Settings in admin to configure

## Usage

### For Administrators:

1. **Setup Moderators**: Create users with 'moderator' role
2. **Set Sequences**: Assign sequence numbers in Sequence & Status page
3. **Assign Products**: Link products to moderators in Product Assignments
4. **Monitor**: View assignments in Recent Assignments page

### For Moderators:

1. **View Orders**: Access "My Orders" in admin menu
2. **Process Orders**: See only orders assigned to you
3. **Update Status**: Change order status as needed

## Frequently Asked Questions

### How are orders assigned?

Orders are assigned based on:
1. Product specialization (moderators only get orders for their assigned products)
2. Round-robin sequence (fair distribution among eligible moderators)
3. Active status (only active moderators receive orders)

### Can I manually assign orders?

Yes, you can manually reassign orders from the order edit page in the "Assign Moderator" section.

### What happens if no moderators are assigned to a product?

The order will not be assigned to any moderator and an admin note will be added.

## Changelog

### 1.0.1
* Initial release
*