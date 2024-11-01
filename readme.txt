=== WooCommerce Shipping Method: R+L Carriers  ===
Contributors: rlcarriers
Tags: woocommerce, shipping, ltl, freight, R+L, R&L, RL, R+L Carriers, R&L Carriers, RL Carriers
Donate link: http://www2.rlcarriers.com
Requires at least: 4.9
Tested up to: 6.5.5
Requires PHP: 7.0
Stable tag: 1.8.3
WC requires at least: 7.8
WC tested up to: 8.9.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The R+L Carriers Shipping Plugin adds your R+L Carriers shipping rates to WooCommerce cart and checkout pages.

== Description ==

The R+L Carriers Shipping Plugin seamlessly connects WordPress and WooCommerce with your R+L Carriers account allowing store owners to offer precise LTL freight rates within the cart and checkout.

Endlessly flexible and customized with small businesses in mind, the R+L Carriers Shipping Method allows store owners to grow their online WooCommerce store by offering products of all shapes and sizes.


= Your rates, every time =

Rates specific to your MyRLC account are retrieved for each customer shopping cart session and are based on cart product shipping attributes and quantities.

Support for standard weight and NMFC-based rates as well as negotiated rates. Optionally offer or add special delivery services such as liftgate or inside delivery to each shipping charge.

* Don’t have a MyRLC account? [Sign up today](http://www2.rlcarriers.com/company/myrlc-signup).

= Support for the latest features =

The R+L Carriers shipping method supports the latest features available in the WooCommerce 3.1.1+.

With full support for LTL freight shipping zones and enhanced AJAX cart/checkout operations, you can focus on growing your business.

= Custom-tailored to your needs =

Customize your R+L Carriers shipping rate preferences with minimum guaranteed rates, fallback rates, and service days display.

Flexible service level settings allow for rate price adjustments, offering specific service levels only, and customization of service level names and descriptions.

= Built with Small Business in mind =

Tailor your WooCommerce store to ensure the only the most precise shipping rates are offered to your customers.

*Minimum and Forced Freight Weights:* Reduce abandoned carts by controlling when LTL rates are offered.

*Force Shipping Class:* Get up and running quickly by setting a site-wide NMFC Shipping Class for your entire store.

= Need More Features? =

The R+L Carriers website offers these additional features:

* Seamless Rate Quotes
* Bill of Lading Generation
* Pickup Request Scheduling
* Shipment Tracing
* Pickup Notification Emails
* Freight Classifications And Hazmat Support

This full-featured shipping platform is the complete solution for all of your freight shipping needs. R+L Carriers has you covered. 

Check out our website for more information regarding the complete platform, at [http://www2.rlcarriers.com](http://www2.rlcarriers.com).

== Installation ==

= Minimum Requirements =

* WordPress v4.9
* WooCommerce v3.1.1
* PHP version 5.6.30 or greater
* MySQL version 5.7 or greater
* An R+L Carriers MyRLC account and B2B API Key

= Quick Install =

* Activate the R+L Carriers Shipping Plugin from the Plugins admin dashboard screen
* Visit the RLC Settings page via the Admin Menu link, enter your **B2B API key** & Save Settings
* Visit the WooCommerce Settings page Shipping tab
* Add the RLC shipping method to your existing shipping zones.
  * Add the RLC shipping method to the Rest of World zone.
* Click RLC shipping method to edit the instance settings
* Enter the following fields at a minimum:
  * Origin City
  * Origin State
  * Origin Zipcode
* Save Settings
* See Product Catalog Configuration below.

Don’t have a MyRLC account? [Sign up today](http://www2.rlcarriers.com/company/myrlc-signup).

= Plugin Configuration and Setup =

To configure WooCommerce properly:  Click on the WooCommerce menu and following the Settings link to the General tab. 

Ensure the following configuration options are set:

Base Location: Must be set to a U.S. state or Canadian province.

Currency: Must be set to US Dollars $.

= Shipping Zones =

*WooCommerce Version 2.6+ Only*

From the WooCommerce menu, click the Settings link and visit the Shipping Zones link under the Shipping tab.

Add the R+L Carriers to all your shipping zones, including the Rest of the World Shipping Zone.

= Mandatory Zone Instance Settings =

The minimum settings to receive rate quotes.

* Origin Zip: The zip code from which orders will be shipped.
  * Accepted Syntax: 5-digit (#####), zip+4 (#####-####)
* Standard Services: Must be checked.
* R+L Carriers API Key:  B2B Tools API Key used to retrieve custom rates.

= Zone Configuration =

*Note:* See the [Freight Rules Tariff](http://www2.rlcarriers.com/freight/shipping-documents/rules-tariff) document for definitions to the following accessorial terms.

* Origin City
* Origin State/Province
* Origin Zip
* Origin Country
* Origin Accessorials
  * Inside Pickup
  * Limited Pickup
  * Origin Lift-gate
  * Airport Pickup
* Destination Countries Allowed: Choose the specific countries where rates will be offered. Hold Ctrl to select multiple.
* U.S. State Exclusion: Select Exclude Specific States and choose the specific states where rates will be offered.
* Force NMFC Class: Ignores product shipping classes and forces all Rate Quote Requests to have items of this single NMFC Class.
* Highest Ship Class: Use the highest NMFC class in a given cart package for the entire package request.
* Minimum Freight Weight: Packages which weigh below this number will not receive Freight Rates. Leave blank or enter 0 to disable and request rates for all carts, regardless of weight.

= Zone Instance Rate Options =
* Fallback: Offer this amount for freight shipment charges if rates are unavailable or the quote request failed.
* Minimum Freight Rate: Freight rates will always be at least this much after adjustments.
* Show Service Days: Display the service days number with the shipping cost label at cart and checkout.

To customize the shipping level options, use the Services table at the bottom of the R+L Carriers shipping method options page. The following customizations are available:

* Name: Modify the title of the shipment service level as displayed on the checkout page.
* Enabled: Show/hide the service level option on the checkout page.
* Price Adjustment ($): Add a fixed dollar amount cost adjustment to a service level.
* Price Adjustment (%): Add a percentage of the cart total to a service level.

= Global Options =

The R+L Carriers Global Settings page can be found by locating the R+L Carriers logo in the WordPress administrative dashboard left menu.

* **Required** Production API Key: Enter your B2B Tools API Key

* Debug Mode: Enable full API request/result logging, as well as display of notices, warnings, and errors.  Please use when sending troubleshooting requests to apisupport@rlcarriers.com.

* Rates & Packing
  * Force Freight Threshold: Shopping Cart Packages above this weight will be forced freight, excluding all other shipment methods. Leave blank or 0 to disable.
  * Packing Method: Change this option to enable Rate Quote Requests to be formatted as Pallet Rate Requests.
  * Package Type Default: Set this value to change the default Package Type for a product that is used when none is specifically set on the product.
  * Pallet Type: For use in conjunction with Pallet Rates. Choose the specific pallet type to use with all Pallet Requests.
  * Pallet Weight Limit: Enter the maximum per-skid weight for pallet requests. The total package request weight will be divided by this weight amount to determine the total number of pallets in a request.

* Special Shipping Services: Check to offer Accessorial Services during checkout

* Toggle Destination Accessorial Services: Check to enable the display of individual Destination Accessorial Options

* Override Destination Accessorial: Check to force destination accessorial service options (removes individual selection during checkout if enabled)


= Product Catalog Configuration =

Generating freight rate quotes requires extra information in addition to what is normally required to generate parcel shipping quotes.

On the WooCommerce General Settings page, click the Products tab to modify the product catalog configuration options:

* General
  * Weight Unit: Must be set to lbs (pounds).
  * Dimensions Unit: Must be set to in (inches).

= Product Creation =

The following product fields must be entered into the Product Data meta box to retrieve LTL freight rates quote from R+L Carriers:

* General
  * Regular Price: Required to add item to a shopping cart.
* Shipping
  * Weight: Product weight is required to retrieve freight rates.
  * Shipping Class: A Valid NMFC is required.

== Frequently Asked Questions ==

= I installed the plugin but rates are not showing up =
1. Make sure you've entered your API key here: YOURWEBSITE/wp-admin/admin.php?page=wc-rlc-settings
2. Make sure you've added weight, dimensions and shipping class to each product. If you do not know the shipping class of a product, please contact the rates department - [Contact R+L Rates Department](https://www2.rlcarriers.com/contact/contactform). The plugin cannot get rates without the weight, dimensions and shipping class.
3. Make sure you’ve set a shipping zone with the R+L Carriers shipping method. Click here to learn more about [setting up shipping zones in WooCommerce] (https://docs.woocommerce.com/document/setting-up-shipping-zones/)
4. In the Woocommerce Zone Shipping Method setting for R+L Carriers ex: /wp-admin/admin.php?page=wc-settings&tab=shipping&zone_id=1, you must enter an origin address. i.e. The location from which the items will be shipped.
5. In the main R+L Shipping settings ex: /wp-admin/admin.php?page=wc-rlc-settings, check the box to turn on Debug Mode. Add items to the cart and any errors encountered while getting rate quote will be displayed.


= Can the plugin be expanded to offer additional features like Shipment Tracing? =
No. This plugin is offered without additional feature capabilities. However, R+L does offer our Shipping Platform which includes a host of other features like BOL generation, Pickup Request Scheduling, Hazmat and Freight Classification support, Shipment Tracing, Pickup Notifications and more. You learn more, here.

= Does the plugin require WooCommerce to work? =
Yes. This plugin is a WooCommerce plugin, specifically, and it requires that platform to function.

= Does the plugin support other freight carriers other than R+L? =
Currently, our plugin does not provide for additional carriers.

= Does the plugin support shipping from multiple origin locations? =
No. All rate quotes will use a single origin zip code that is set in the woocommerce shipping settings.

= Can I set individual products to be shipped on different pallet types? =
No. Pallet type is a global setting that will apply to all products equally.

= Does this plugin support Canadian shipments? =
Partially. R+L will only provide a rate quote for shipments that have either their origin or destination in the U.S. If your origin city is in Canada then you will need to restrict the shipping zone to U.S. destinations only. Domestic Canadian shipments are not currently supported.

== Screenshots ==

1. Shopping Cart Totals with R+L Carriers Rate Quotes
2. Special LTL Delivery Services
3. Order Thank You Page with R+L Quote #
4. R+L Carriers Global Settings
5. R+L Carriers Zone Instance Settings

== Changelog
= 1.8.3 =
* Updated how rate quotes are handled for ocean shipments (AK and HI)
* Ensured that Overdimension quotes are handled properly for ocean shipments
= 1.8.2 =
* Since the dimensions are optional for Rate Quotes, added filter to make sure null Dimensions wouldn't cause issues.
= 1.8.1 =
* Added files that were missing from initial update to REST
= 1.8.0 =
* Updated the plugin to handle REST instead of SOAP.
= 1.7.3 =
* Updated how the plugin handles Overdimension Freight.  Updated the SOAP endpoint the plugin points to.
= 1.7.2 =
* Updated the plugin version number in the core PHP file to reflect the new version correctly.
= 1.7.1 =
* Tested the plugin to work with WordPress version 5.8.3 and WooCommerce version 6.0.
= 1.7.0 =
* Added function to convert weight to pounds and dimensions to inches when performing a rate quote.
* updated FAQ section
= 1.6.1 =
* updated tested up to tag and FAQ section
= 1.6 =
* Fixes bug to show rate quote number on order
* Fixed bug where non logged in customers could not get updated rates based on accessorial selection
= 1.5.1 =
* updating version number
= 1.5 =
* Fixed error caused by new shipping states include
= 1.4 =
* Improved error handling
= 1.3 =
* Fixed condition checking to make sure each product has a shipping class
= 1.2 =
* Fixed settings to allow selection of Canadian Provinces. Updated FAQ.
= 1.1 =
* Added error handling
= 1.0 =
* Initial Release

== Upgrade Notice ==
= 1.3 =
Products that are missing a shipping class should no longer be missed by the condition which is checking for it.

= 1.2 =
Necessary for shipping from origin cities in Canada.

= 1.1 =
Additional error handling will prevent edge case failure scenarios from interfering with your site.
