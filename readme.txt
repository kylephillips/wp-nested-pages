﻿=== Nested Pages ===
Contributors: kylephillips
Donate link: http://nestedpages.com/
Tags: pages, admin, nested, tree view, page tree, sort, quick edit, structure
Requires at least: 3.8
Tested up to: 4.6
Stable tag: 1.6.3

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Nested Pages provides a drag and drop interface for managing pages & posts in the WordPress admin, while maintaining quick edit functionality.

== Description ==

**Why use Nested Pages?**

* Provides a simple & intuitive drag and drop interface for managing your page structure and post ordering
* Enhanced quick edit functionality
* Adds an editable, sortable tree view of your site's page structure
* Automatically generates a native WordPress menu that matches your page structure
* A way to quickly add multiple pages & posts (ideal for development)
* Works with any post type
* Works on touch-enabled devices

For more information visit [nestedpages.com](http://nestedpages.com).

**Important: Nested Pages requires WordPress version 3.8 or higher, and PHP version 5.3.2 or higher.**

**Languages:**

* Danish (Thomas Blomberg)
* Dutch (Arno Vije)
* English
* Finnish (Roni Laukkarinen)
* French (Nico Mollet)
* German/Swiss German (Bartosz Podlewski)
* Italian (Francesco Canovi)
* Portuguese (Luis Martins)
* Russian (Алексей Катаев)
* Spanish (Raúl Martínez)
* Swedish (Marcus Forsberg)
* Turkish (Yuksel Beyti)

== Installation ==

1. Upload wp-nested-pages to the wp-content/plugins/ directory
2. Activate the plugin through the Plugins menu in WordPress
3. Click on the Pages Menu item to begin ordering pages. Nested Pages replaces the default Page management screen.
4. To access the default the pages screen, select Default Pages located in the Pages submenu, or on the Nested Pages screen
5. Additional options are available through the plugin settings at Settings > Nested Pages. Nested Pages may be enabled for any post type, and is configurable by post type. Options include quick edit field settings, thumbnail display and more.

== Frequently Asked Questions ==

= Can I use Nested Pages with other post types? =
As of Nested Pages version 1.3.0, all post types are supported. To enable the Nested Pages interface for post types, update your settings under Settings > Nested Pages.

= Is this plugin compatible with the WPML plugin? =
Nested Pages is not currently compatible with WPML. Why? Primarily due to WPML's own menu generation options. It is not a matter of making the plugin translatable, but a core plugin feature that is not supported.

= How do I access the WordPress "Pages" screen? =
Click the “Default link in the page subnav, or on the Nested Pages screen. This item may be optionally hidden under the Nested Pages settings.

= How do I save the order I create? =
Post sorting and nesting is saved in the background after changes are made to the structure. If the "manual page order" option is enabled in the plugin options, you'll need to click the "Sync Order" button at the top of the page to save.

= How do I edit in bulk? =
As of version 1.6, bulk edits are available directly through the Nested interface. To edit in bulk, select one or more posts. Choose "Edit" from the bulk actions to edit. Bulk edit functionality is similar to the WordPress core edit. Note: custom links that have been added through the Nested Pages interface are not affected by bulk edit actions.

= What about custom columns? =
As of version 1.6.4, custom columns are available. Visit Settings > Nested Pages > Post Types to enable and order custom columns for each post type. Columns made available through the appropriate WordPress filters are available for selection, along with any taxonomies enabled for the post type. Sortable custom columns are not enabled, however. The sorting of columns would interfere with the automatic ordering and menu syncing of Nested Pages.

= What are those dots in my page rows? =
If you have WordPress SEO by Yoast installed, your page score indicators are shown along with the pages.

= Can I show the thumbnail in the tree view? =
As of version 1.6, yes. Visit Settings > Nested Pages > Post Types to configure thumbnail settings for each post type. Filters are also available for customizing the images displayed.


== Screenshots ==

1. Expandable tree view of your page structure

2. Enhanced quick edit offers configurable fields and additional options

3. Sortable page nesting updates in real time

4. Toggle child pages for a clutter-free and nestable tree

5. Quickly add child posts without leaving the page tree

6. Clone existing pages for quick setup

7. Add pages in bulk – great for setting up new site structures quickly

8. Enable or disable nesting for any user group with the necessary permissions. Additional options offer more configuration options.

9. The Nested Pages interface can be enabled on a per-post-type basis, with customizable options for each type.

== Changelog ==

= 1.6.4 =
* Text domain updated to wp-nested-pages in compliance with wordpress.org translation requirments
* Custom columns are now supported. Visit Settings > Nested Pages > Post Types to enable and order custom columns for each post type. Columns made available through the appropriate WordPress filters are available for selection, along with any taxonomies enabled for the post type.
* Additional bug fixes in front end redirects that were causing duplicate slugs to be unreliable

= 1.6.3 =
* Temporary removal of front end redirects (resolves issue of duplicate slugs being overriden)
* Bug fix in fatal error with integration with Editorial Access Manager (Thanks to Marco Chiesi)

= 1.6.2 =
* Bug fix that was throwing error in the nav menu

= 1.6.1 =
* Bug fix in nav menu front end that was throwing error on sites with errors enabled.

= 1.6.0 =
* Redesign of post type settings page, with additional options added.
* Thumbnail support added to nested/sort view. Visit Settings > Nested Pages > Post Types to enable post thumbnails and set options.
* Ability to customize quick edit fields for each post type added. Visit Settings > Nested Pages > Post Types to hide specific fields from the quick edit interface for each post type. Please note: specific fields are still hidden depending. on the current user's roles/capabilities.
* Bulk delete functionality added.
* When adding multiple pages, the option to set them as hidden in the nav menu is now available.
* Option added to manually sync the nav menu and page order.
* Bug fix where Post menu wasn't being replaced if option checked.
* Modified date set to not update when reordering post order through the nested view.
* Issue of duplicate posts item in nav menu resolved.
* Fix for invalid rel attribute in generated nav menus, resulting in W3C validation error.
* Filters added for title, edit links, and view links in the nested interface.
* Bug fix where non-hierarchical post types were allowing nesting.
* Bug fix where page redirect errors were showing while attempting to delete pages with the Page post type disabled. (Thanks to Evan Washkow)
* Bug fix where non-ascii characters were not displaying correctly in the quick edit slug field.
* Bug fix where "Add Child" was not available if menu sync was disabled completely.
* Tab index set when adding a new child or multiple pages. No mouse necessary for adding in bulk.
* Various UI enhancements.
* Swedish translation added (Thanks to Marcus Forsberg).

= 1.5.4 = 
* Spanish translation added (Thanks to Raúl Martínez)
* Various bug fixes related to WordPress 4.4 update
* Duplicate menu items bug fix

= 1.5.3 =
* Confirmation modal added when deleting a link item.
* Bug fix where custom nav titles were not being saved.
* Bug fix where replacing the default menus was breaking custom admin submenu links.
* Minor interface enhancements.

= 1.5.2 =
* Fixed bug when upgrading in an install with sync disabled.

= 1.5.1 =
* Bug fix where hidden nav items in the nested view were deleting nav items from other menus.
* Updated German Translation (Thanks to Martin Wecke)

= 1.5.0 =
* Links now include all taxonomies/post types, enabling full control over the primary site menu from the Nested Pages interface. Start adding menu items by selecting "Add Link" from the top, or the link button on a specific row to add a child item.
* Escape key closing of modal windows added.
* Category filtering added to pages if categories enabled

= 1.4.1 =
* Bug fix in quick edit where child pages display parent row data on update.

= 1.4.0 =
* Clone/Duplicate functionality added - click the "clone" button in a row to clone/duplicate a post or page
* Bug fix when attempting to trash Advanced Custom Field field groups (Thanks to Ben Plum)
* Javascript rewritten for more future-friendly updates and feature builds
* Draft filter added to list
* Tested and confirmed WordPress 4.3 compatibility

= 1.3.15 =
* Minor Bug fixes and optimizations
* Bug fix in expand all button
* Support added for page that are noindexed in WordPress SEO (Thanks to Joost de Valk)

= 1.3.14 =
* Minor bug fix – modal not appearing when last item in the trash (provided by ClementRoy)
* Option added to hide the "Sync Menu" checkbox (visit Settings > Nested Pages > General to hide)
* Updated Danish Translation (Thomas Blomberg)
* Confirmed compatibility with WordPress v4.2

= 1.3.13 =
* Bug fix preventing some custom post types from being enabled
* Bug fix - editors with sort capabilities menu sync enabled
* WP Engine modal z-index fix

= 1.3.12 =
* Permissions Bug fix in emptying trash (Thanks to Yuksel Beyti)

= 1.3.11 =
* Minor UI bug fixes
* Javascript Modal error bug fix
* Turkish Translation (Provided by Yuksel Beyti)
* Updated French Translation (Provided by @Inovagora)

= 1.3.10 =
* Bug fix - resolves deprecated function issue with SEO by Yoast update v1.7.3. Critical for sites running both Nested Pages and WordPress SEO by Yoast

= 1.3.9 =
* Bug fix - error when deleting a page from the nested view with menu sync disabled

= 1.3.8 =
* Bug fix – Critical error that was overriding existing menu items outside of the Nested Pages generated menu. Other menus are now unaltered on save.

= 1.3.7 =
* Bug fix - error when deleting the generated menu

= 1.3.6 =
* Bug fix – error preventing new install resolved

= 1.3.5 =
* Minor bug fixes
* Editorial Access Manager Plugin Integration

= 1.3.4 =
* Minor bug fixes
* Minor UI enhancements
* Changes to page and link menu items under appearance > menu now sync the Nested Pages listing when menu sync is enabled (other custom menu item types not yet supported).
* Option added to disable nesting on hierarchical post types while maintaining sort functionality (ideal for live sites where link structures need to remain intact)
* Updated Dutch Translation (Provided by Arno Vije)
* Search capabilities added
* Hash/Empty URLs no longer appended with http://

= 1.3.3 =
* Russian Translation (Provided by Алексей Катаев)
* Minor bug fix in add child page functionality that effects display of appended pages.

= 1.3.2 =
* Bug fix in menu - pages now nestable under links.

= 1.3.1 =
* UI enhancements in Quick Edits – default date fields replaced with datepicker and formatted time. 

= 1.3.0 =
* All public post types are now supported, both hierarchical and non-hierarchical. To enabled the Nested Pages interface for additional post types, visit Settings > Nested Pages and select the "Post Types" tab. The generated nav menu is tied to the pages type, which is enabled by default.
* New interface for adding top-level posts/pages in bulk
* New "Empty Trash" link for quickly emptying trash on enabled post types
* Dutch translation (Provided by Arno Vije)

= 1.2.1 =
* Bug fixes when using custom roles (Thanks to Luis Martins for troubleshooting help)

= 1.2.0 =
* PHP 5.3.2+ is now required to run Nested Pages. Will not run or install on older versions of PHP.
* Visual nesting indication limit removed
* Portuguese Translation (Provided by Luis Martins)
* Various bug fixes

= 1.1.9 =
* Minor bug fixes in editor capabilities
* Italian translation (Provided by Francesco Canovi)

= 1.1.8 =
* New Child Pages Interface - Add child pages more efficiently with the new add child pages dialog. Add a single child page without leaving the Nested Pages view, or add multiple pages with one click. Reorder multiple child pages before saving with the drag and drop interface you're accustomed to.
* Tested for 4.1 compatibility
* Page ID indicator added to Quick Edit dialog
* Taxonomies & other custom menu items now visible in pages admin menu

= 1.1.7 =
* Danish Translation (Provided by Thomas Blomberg)
* Finnish Translation (Provided by Roni Laukkarinen)
* German/Swiss German Translation (Provided by Bartosz Podlewski)
* Added option to hide default pages
* Added option to give editors ability to sort pages
* Query filter added to main page listing

= 1.1.6 =
* Minor UI Improvements - Current admin page now highlighted
* Page post type bug - now verfied before plugin activation
* Multisite bug fixes
* French Translation (Provided by Nico Mollet)

= 1.1.5 =
* Menu Sync bug fixes
* Localization bug fixes

= 1.1.4 =
* Password/Private functionality added to page quick edit
* Flat taxonomy support added to page quick edit
* Quick edit UI enhancements
* Cross-domain icon font issue addressed

= 1.1.3 =
* Option to customize the generated nav menu added

= 1.1.2 =
* Status bug fix in pages view

= 1.1 =
* Expanded/Collapsed states now saved for each user
* Trash functionality added
* Trashing pages now redirects to Nested Pages view
* Trash link added to quickly get to a list of trashed pages
* New "Add link" functionality – creates custom link menu items
* Additional options added for generated menu items - title attribute, css classes, link target

= 1.0 =
* Nested Pages

== Upgrade Notice ==

= 1.3.12 =
Resolves issue with custom user roles/permissions and deleting posts. Important patch for sites using custom user roles. Thanks to Yuksel Beyti for finding/patching.

= 1.3.10 =
Resolves deprecated function issue with SEO by Yoast update v1.7.3. Critical for sites running both Nested Pages and WordPress SEO by Yoast

= 1.3.8 =
Critical bug fix in saving menus. Existing menus outside of the generated menu now unaltered.

= 1.3.4 =
Minor bug fixes and expanded menu functionality.

= 1.3.3 =
Russian translation added along with minor bug fixes.

= 1.3.2 =
Minor bug fixes in menu.

= 1.3.1 =
Date fields in Quick Edit windows are now replaced with a date picker and formatted time. If the formatting conflicts with your specific locale, disable the datepicker under Settings > Nested Pages > General.

= 1.3.0 =
All post types are now supported. Also includes minor bug fixes and UI improvements.

= 1.2.1 =
Bug fix when using custom roles. 

= 1.2 =
PHP 5.3.2 now required – Nested Pages will not install on older versions of PHP. If you are running less than 5.3.2, continue to use Nested Pages version 1.1.9.

= 1.1.9 =
Italian translation included along with minor bug fixes.

= 1.1.8 =
New Child Pages Interface, various UI enhancements

= 1.1.6 = 
Minor UI enhancements and bug fixes.

= 1.1.5 =
Various bug fixes in the menu system and localization.

= 1.1.4 =
Added additional quick edit functionality along with UI enhancements.

= 1.1.3 =
Added option to rename the generated nav menu.

= 1.1.2 =
Includes fix for pages view that was preventing draft and private pages from being loaded.

= 1.1 =
Several new features have been added in version 1.1, including: saved toggle states, additional menu options, trash functionality, ability to add "link" menu items, and more.

== More Information ==

= Generated Menu =
The default menu generated automatically is named "Nested Pages". You may rename the menu under Appearance > Menus, or under the Nested Pages settings.


= Toggling the Page Tree =

To toggle the child pages in and out of view, click the arrow to the left of a parent page. To quickly expand and collapse all pages, click the button in the upper right corner of the Nested Pages Screen. 


= Theme Use =

To order by nested pages ordering in your theme, use the `menu_order` order option in your queries. 


= Hiding Pages from the Tree View =

To hide a page from the tree view, open the quick edit form, select the option to “Hide in Nested Pages” and click Update to save the change.

To toggle the page back into view, click the “Show Hidden Pages” link at the top of the screen. The hidden pages are now visible, and can be re-edited to be shown.


= Sorting Pages =

To sort pages, hover over the page row. A menu icon (three lines) will appear. Click (or tap) this icon and drag to reorder within the menu. To drag a page underneath another, drag the page to the right and underneath the target parent. Visual indication is provided with an indentation. The drag and drop functionality works similarly to WordPress menus.

= Menu Sync =

After installing Nested Pages, a new menu will be available with the name `Nested Pages`. By default, menu syncing is enabled. To disable the sync, uncheck “Sync Menu” at the top of the Nested Pages screen. Recheck the box to enable it again and to run the sync. 

**Saving Performance:** If your site has a very large number of pages, disabling page sync may help speed up the save time when using Nested Pages.

**Editing the generated menu:** Any manual changes made to the menu outside of the Nested Pages interface will be overwritten after the synchronization runs.

**Hiding Pages in the Menu:** To hide a page from the Nested Pages menu, click “Quick Edit” on it’s row, select “Hide in Nav Menu”, and click “update”. If menu sync is disabled, enable it now to sync the setting. Hidden pages are marked “(Hidden)”. If a page with child pages is hidden from the menu, all of it’s child pages will also be hidden. 

= Filters =

* `the_title($title, $post_id, $view)` – Standard title filter. Applied to the title displayed in the nested interface. A third paramater, $view, is passed to check if the current title is being displayed in the nested view.
* `nestedpages_thumbnail($image, $post)` – Customize the thumbnail for each page/post. Note: Thumbnails must be enabled for the post type.
* `nestedpages_thumbnail_fallback($image, $post)` - Customize the thumbnail fallback for each page/post (if the post does not have a featured image). Note: Thumbnails must be enabled for the post type.
* `nestedpages_edit_link($link, $post)` - Customize the "edit" link for each page/post in the nested interface.
* `nestedpages_edit_link_text($text, $post)` - Customize the "edit" link text for each page/post in the nested interface.
* `nestedpages_view_link($link, $post)` - Customize the "view" button link for each page/post in the nested interface.
* `nestedpages_view_link_text($text, $post)` - Customize the "view" button text for each page/post in the nested interface.