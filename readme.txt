=== Omnisend for Woocommerce ===
Contributors: Omnisend
Tags: omnisend, woocommerce
Requires at least: 4.0.1
Tested up to: 4.3
Requires PHP: 5.6
Stable tag: 4.3
License: GPLv3 or later License
URI: http://www.gnu.org/licenses/gpl-3.0.html

-------------------------
VERSION 1.3
-------------------------

Plugin requires actual Omnisend accountID and active Woocommerce plugin

- Adds possibility to enter Omnisend account ID to paste code snippet in footer

After successfull configuration:

- All PRODUCTS will be POSTed or updated in Omnisend;

- All CONTACTS will be POSTed or updated in Omnisend

- CART will be posted to Omnisend every time when product will be added (processed on Frontend via Ajax)

- All ORDERs will be POSTed to Omnised after ORDER confirmation



V.1.3 Additions

- New project structure
- New folder for assets created
- Now Model classes are responsible for preparing Products, Carts, Contacts and Orders for Omnisend
- Manager class now responsible for manipulations with Model classes and Omnisend
- Helper class contains method for checking API key and Omnisend cUrl wrapper
- Now after successfull API key update both: Products and Contacts will be synchronized with Omnisend
- Removed Contact List creation
- Now Cart will be Deleted from Omnisend after Order confirmation
- Product will be deleted from Omnisend after placing in trash
- Product will be POSTed to Omnisend after restoring from trash ( doesn't work yet for Variable products)
- Fixed validations for emails and phoneNumbers 