=== Nested Pages ===
Contributors: kylephillips
Donate link: http://nestedpages.com/
Tags: pages, admin, nested, tree view, page tree, sort, quick edit, structure
Requires at least: 3.8
Tested up to: 5.2
Requires PHP: 5.4
Stable tag: 3.1.6
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

**Important: Nested Pages requires WordPress version 3.8 or higher, and PHP version 5.4 or higher.**

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
Yes! Visit Settings > Nested Pages > Post Types to enable the interface for any public post type. There are settings available for each post type, allowing customization of the admin to fit your needs.

= Is this plugin compatible with the WPML plugin? =
Please see the section titled "WMPL Compatibility" for details on using Nested Pages with WPML. In short, ordering and nesting functionality is available, but automatic menu generation across languages is limited. It is advised to use WPML's menu synchronization for this purpose.

= How do I access the WordPress "Pages" screen? =
Click the “Default link in the page subnav, or on the Nested Pages screen. This item may be optionally hidden under the Nested Pages "Post Type" settings.

= How do I save the order I create? =
Post sorting and nesting is saved in the background after changes are made to the structure. If the "manual page order" option is enabled in the plugin options, you'll need to click the "Sync Order" button at the top of the page to save.

= How do I edit in bulk? =
To bulk edit, check the checkbox on the pages/posts you'd like to edit. Once one or more have been selected, a "Bulk Actions" dropdown will appear, allowing you to either move the selected posts to the trash or bulk edit.

= What about custom columns? =
Custom columns are not currently supported by Nested Pages. To view custom columns, click on the "Default" link to view the native interface. If you are using WordPress SEO by Yoast, a page analysis indicator is shown.

= What are those dots in my page rows? =
If you have WordPress SEO by Yoast installed, your page score indicators are shown along with the pages.

= Can I show the thumbnail in the tree view? =
Yes! Visit Settings > Nested Pages > Post Types to configure thumbnail settings for each post type. Filters are also available for customizing the images displayed, as well as specifying a fallback image in the case that the post does not have a thumbnail assigned.

= Nested Pages changes my permalink structure. How do I stop that? =
Nested Pages uses the same ordering methodology as WordPress core. The plugin offers an enhanced interface to achieve the same results. Parent/Child relationships are used in conjunction with the post menu_order field to nest and order posts/pages. There is currently no option to disable this core feature.

= Can I generate a menu using a custom post type? =
No. The menu synchronization currently only works within the pages post type. 


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

= 3.1.7 =
* Updates quick edit interface.
* Adds filters for "Sticky" text indicator in post rows. 

= 3.1.6 =
* Adds filters for making "Make Sticky" quick edit form field available for any post type. 
* Adds filter for customizing the "Make Sticky" label text.
* Bug fixes in submenu display when using the admin customization feature.
* Adds new feature to hierarchical post types that allow trashing of pages and all children.
* Fixes bug where adding/appending a child post/page was not saving the correct menu order

= 3.1.5 =
* Removes link item in dropdown from non-page hierarchical post types, replaces with filterable boolean
* Enhances modal UI when deleting an item

= 3.1.4 =
* Adds support for custom statuses, configurable by post type. To enable statuses for a specific post type, visit the plugin settings: Settings > Nested Pages > Post Types. Toggle the post type. If there are custom statuses available, a field titled "Enable Custom Statuses" will be available to select statuses.
* Updates edit target for "link" row items to link to edit post screen where applicable.
* Updates modal UI for adding multiple pages and adding links

= 3.1.3 =
* Updates nesting interface to use an indented style. To revert to the previous format, visit Settings > Nested Pages and select the checkbox titled "Use the classic (non-indented) hierarchy display."
* Fixes bug where submenus were being removed after saving a new admin menu customization and reordering the item.
* Enhances translation capabilities by replacing hardcoded strings with proper I18n functions.

= 3.1.2 =
* Adds filter to role capabilities (for adding items to customized admin menus with custom capabilities).

= 3.1.1 =
* Removes ability to hide Settings and Nested Pages menus from administrators within the admin customization interface (removing items hides links to necessary admin sections).
* Adds ability to reset admin menu settings by clicking a single button
* Fixes bug in new admin menu feature where submenus added by other plugins were not being displayed if a custom menu was in use.

= 3.1.0 =
* Adds new feature for customizing the order, visibility, and labels of the admin menu. To view the feature, visit Settings > Nested Pages > Admin Customization > Admin Menu. Important: once custom menus have been configured, new menu items added by other plugins may not appear upon activation. To add these items, revisit the Nested Pages settings and reconfigure the menus with the new items (they will appear at the bottom of the customized menu in the drag and drop interface). While menus may be configured for each user group, the plugin does not set any permissions. While items may be hidden from the menu, they will still be accessible with a direct link if the user has the appropriate permissions.
* Adds filters for adding basic custom fields to the quick edit interface. Currently supported field types include text, date, and select. See https://gist.github.com/kylephillips/236d0a90aa2ea6fb628c5c1e4010f7be for example usage.

= 3.0.11 =
* Adds filters for "Sort/Nested View" and "Default" labels.
* Fixes issue where serialized meta data was not being saved properly during cloning.

= 3.0.10 =
* Fixes issue with translation file naming that was preventing plugin translations from loading.
* Adds settings action to reset user preferences (toggled/visible pages). For clearing user meta that may has become unnormalized during site/other plugin updates, resulting in PHP errors in the listing view.

= 3.0.9 =
* Fixes Javascript bug introduced in v3.0.8 resulting in console error on page edit screen.

= 3.0.8 =
* Security Fix: Fixes bug where contributors could quick edit posts not authored by themselves.
* Adds filters for displaying individual row action/links. 
* Fixes bug where submenu was not expanded when editing a single page, and "replace default" is selected.
* Fixes bug where toggle icon still displays when all child items are in the trash.
* Adds filters for taxonomies and terms in the listing interface.
* Fixes bug where setting a link item to hide in nav menu doesn't remove the associated menu item.

= 3.0.7 =
* Adds support for "Dark Mode" plugin.
* Tested in WordPress v5

= 3.0.6 =
* Fixes issue introduced in v3.0.3 where some custom post/page dropdown fields were failing to show.

= 3.0.5 = 
* Adds actions for sorting update. Single post: nestedpages_post_order_updated($post_id, $parent, $order). All posts: nestedpages_posts_order_updated($posts, $parent).
* Adds filter to disable sorting per post: nestedpages_post_sortable($sortable, $post, $post_type).

= 3.0.4 = 
* Fixes bug introduced in v3.0.3 that prevented selection of privacy page.

= 3.0.3 =
* Fixes PHP error when performing a search with no results.
* Fixes WordPress core issue where children of a private post become unnested after editing the post.
* Fixes bug where updated titles on custom links were not synching with the menu.
* Fixes bug where an extra trailing slash was being added to asset URLs, resulting in style errors.
* Adds capability conditional for quick edit functionality
* Adds filter to hide quick edit button (receives post object as parameter)

= 3.0.2 =
* Fixes issue where post_row_action error was being thrown due to incorrect object type being passed as parameter.

= 3.0.1 =
* Breaking Change: PHP Version 5.4.0+ is required to run Nested Pages V3+.
* Parent field added to bulk edit under hierarchical post types.
* Fixes HTML Validation where invalid rel attribute error was being thrown on custom link items.
* Fixes bug where filtering by taxonomy did not work.
* Adds error handling for existing nav menu with the name “Nested Pages”, preventing WP_Error object from saving in the database which causes a fatal error in some instances.
* Adds support for custom post states, using the display_post_states filter (Ex: Showing a WooCommerce - Checkout Page designator).
* Adds support for custom row actions using the post_row_actions and page_row_actions filters.
* Adds new feature for inserting new posts before/after a selected post.
* Adds new option to set a default "order by" and "order" parameter for post types, including pages. Visit the post type settings tab in the plugin settings to enable (Note: Sorting/nesting will not be available when viewing the lists in any combination other than menu_order/ascending).
* Fixes issue where disabling nesting of a post type was not working.
* Adds template names as CSS classes for custom row styling.
* Adds enhancements to the user interface.
* Removes capability to nest a page underneath a link, preventing broken links.
* Fixes issue where meta values were not being saved during cloning, when the source post contained multiple entries with the same meta_key.
* Fixes bug where new page modals were not displaying when WPML was installed.

= 2.0.4 = 
* Fixes PHP warning if a search is performed with only one result when WP_DEBUG is enabled.
* Fixes "undefined variable: c" PHP warning on some installs when WP_DEBUG is enabled. 
* Tested for WordPress 4.9 compatibility

= 2.0.3 =
* Fixes bug where empty trash was not working under custom post types.
* Fixes issue where sticky status could not be removed from post in quick edit.
* Adds button to reset all plugin settings to default.

= 2.0.2 =
* Fixes issue where no posts were displaying in nested view when a taxonomy was assigned to the post with a dash in the taxonomy name.

= 2.0.1 =
* WPML compatibility added (Limited support, see the "More Information" section for important notes on support).
* Important upgrade note: Custom links within the Nested Pages interface are disabled if WPML is installed and enabled. This is a potential breaking change, so please remove all custom links before updating. Links may be added back to menus by using the default WordPress Appearance > Menus editor.
* Sort/Order options on non-page post type listings may now be added/removed. All options are hidden by default. To enable these, visit Settings > Nested Pages > Post Types. Under the desired post type, options may be enabled under the "Sort Options" row.
* Bug fix where pages hidden from the Nested Pages view where displaying.
* "Sticky" checkbox added to quick edit form under non-hierarchical post types.
* Adds query optimizations to the listing view, reducing the overall number of queries made.

= 2.0.0 =
* WPML beta release

= 1.7.1 =
* Bug fix where saving post type options was enabling all post types

= 1.7.0 =
* Added option to disable sorting per post type.
* Added option to assign a post type page. Assigning a page to a post type adds "Add New" and "All" links to the page row for the given post type, along with a count of published posts for that type.
* 24 hour time format support added to datepicker time field (follows General Time Format settings).
* Custom fields generated by the plugin converted to hidden fields, preventing them from displaying in the post custom field meta box.
* Bug fix where selecting "Hide in Nested Pages" was not saving in the quick edit interface.
* rel=page removed from generated nav menu links which was causing errors in W3C validation.
* Bug fix where menu sync was firing in the background when "Disable menu sync completely" option was selected.

= 1.6.8 =
* Bug fix where admin bars were not displaying in Safari until hover
* Bug fixes in display of post titles with html tags
* Bug fix where date was not being saved in the quick edit form when the datepicker is not enabled

= 1.6.7 =
* Multisite superadmins treated as admins (Thanks to Kristoffer Svanmark)
* Text domain change from v1.6.4 integrated

= 1.6.6 =
* Important security update: XSS vulnerability fix

= 1.6.5.2 =
* Temporarily disabling front end redirects.

= 1.6.5.1 =
* Temporarily rolling back to 1.6.3

= 1.6.5 =
* PHP fatal error bug fix

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


= WPML Compatibility =

As of version 2.0.1, some features of Nested Pages are compatible with WPML. There are some important exceptions to take note of before upgrading to version 2 if WPML is installed, or if installing WPML on an existing Nested Pages enabled site.

**WPML and menu support:** Certain features within Nested Pages are disabled if WPML is installed and enabled. This is due to the complexity of menu synchronization across languages. While automatic menu sync remains available for the site's primary language, additional languages must be synchronized using WPML's menu synchronization methods. If menu sync is enabled within Nested Pages, a "Sync WPML Menus" link is available on non-primary language screens for convenience.

**Custom Links:** Custom links within Nested Pages are disabled on installs with WPML. Custom links may be added through the traditional Appearance > Menus interface. This ensures that WPML menus synchronize successfully across languages.


= Filters =

* `the_title($title, $post_id, $view)` – Standard title filter. Applied to the title displayed in the nested interface. A third paramater, $view, is passed to check if the current title is being displayed in the nested view.
* `nestedpages_thumbnail($image, $post)` – Customize the thumbnail for each page/post. Note: Thumbnails must be enabled for the post type.
* `nestedpages_thumbnail_fallback($image, $post)` - Customize the thumbnail fallback for each page/post (if the post does not have a featured image). Note: Thumbnails must be enabled for the post type.
* `nestedpages_edit_link($link, $post)` - Customize the "edit" link for each page/post in the nested interface.
* `nestedpages_edit_link_text($text, $post)` - Customize the "edit" link text for each page/post in the nested interface.
* `nestedpages_view_link($link, $post)` - Customize the "view" button link for each page/post in the nested interface.
* `nestedpages_view_link_text($text, $post)` - Customize the "view" button text for each page/post in the nested interface.