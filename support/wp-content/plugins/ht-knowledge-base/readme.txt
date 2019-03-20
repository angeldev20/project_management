=== HelpGuru Knowledge Base ===
Contributors: herothemes
Tags: knowledge base, knowledge plugin, faq, widget
Requires at least: 4.3
Version: 3.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

Add a Knowledge Base to WordPress for use with the HelpGuru theme


== Installation ==

It's easy to get started

1. Upload `ht-knowledge-base` unzipped file to the `/wp-content/plugins/` directory or goto Plugins>Add New and upload the `ht-knowledge-base` zip file.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. If upgrading, ensure you deactivate and re-activate the plugin to ensure any upgrade routines are run correctly.



== Frequently Asked Questions ==

= Q. I have a question! =

A. Please consult the HelpGuru Knowledge Base Documentation accompanying the HelpGuru theme

= Q. Category thumbnails are too big =

A. You need to use the `Regenerate Thumbnails` plugin to re-generate the thumbnails to the correct size.



== Screenshots ==



== Changelog ==

- 3.0.0 =

HelpGuru Knowledge Base update
Improved compatibility with HelpGuru
Removed unsupported modules 

= 2.7.11 =

Fixed issue with No articles in this category message
Improved HKB categories widget - added heirarchy support
General code cleanup and i18n improvements

= 2.7.10 =

Improved slugs checking and options
Removed nothing else here message when category contains subcategories
Added check for exits tables
Refactored backend voting styles
Added filter for saving user visits and search queries
Added check to display article attachments for password protected posts

= 2.7.9 =

Using new package builder
Updated no category set warning
Added no articles in KB message
Cleaned up language files

= 2.7.8 =

Fixing settings flash

= 2.7.7 =

Fixing issue with Avast false positive
New pot file for translators - transitional
Code cleanup

= 2.7.6 =

Fixing settings links
Cleaning up settings code

= 2.7.5 =

Hotfix for customizer error

= 2.7.4 =

Fix for article number setting
Fix for TOC widget, will now not display when no headers in article
Fix for language used in options panel
Fix for search issue when WordPress address not site address
Fix for date issue with analytics
JS check to ensure slugs are not the same
Removed KB archive dummy page from pages list

 
= 2.7.3 =

Hotfix for article number setting
Hotfix for exits to display at end of article, defaults to false

= 2.7.2 =

Hotfix for comments setting

= 2.7.1 = 

Hotfix for PHP formatting issues

= 2.7.0 = 

Replaced/Removed Redux framework to improve theme compatibility
Added new search post types filter

= 2.6.4 =

Analytics plugin detect warning hotfix

= 2.6.3 =

Attachment post title name hotfix
Analytics date range hotfix

= 2.6.2 =

TOC Widget hotfix

= 2.6.1 =

Added article excerpt to search result if option enabled
Fixed bug with category icon display
Localization improvements

= 2.6.0 =

Implemented transfers/exits module
Continued work on analytics functionality
Fixes for breadcrumb display
Various fixes for improved compatibility with SEO plugins
Styling improvements


= 2.5.4 =

Hotfix for meta markup in widgets

= 2.5.3 =

Hotfix for article ordering
Hotfix for Redux framework update to 3.5.9.3

= 2.5.2 =

Hotfix for auto uppdater

= 2.5.1 =

Hotfixes for breadcrumbs
Rebasing as 2.5.x

= 2.5.0 =

Minor Bug fixes
WPML search box fix
Adding additional filters
Subcategory display inconsistency fix
Responsive bugs in HKB archive fix


= 2.4.0 =

Added filters for option helpers
Added filters for option sections
Added filter and action hook for options
Fixing responsive bugs
Adding InstaAnswers compability


= 2.3.4 =

Hotfix for titles in Knowledge Base archive
Hotfix for z-index in live-search

= 2.3.3 =

Hotfix for titles in Knowledge Base archive

= 2.3.2 =

Hotfix for WordPress nav menus

= 2.3.1 =

Hotfix for page titles
Hotfix for knowledgebase styles

= 2.3.0 =

Upgraded database functionality, rewrote controllers and additional underpinning for analytics
Added database version check, upgrades performed as required
New metabox for article stats - views, feedback, attachments
Added filters to stop custom content (stop_ht_knowledge_base_custom_content)
Fix for WP REST API
Fix for 404 error when previewing a published article
Fix for sub category depth display
Fix for custom article ordering when order previously set to descending
Fix for category permalink prefixed with blog slug
Fix for sort by article views
Fix for comment template, disqus compatibility
Improvements for network activate

= 2.2.0 =

Fixed issue with breadcrumbs link
Reordered admin menu
Change voting to post request and removed link
Fixed article count of sub-subcategories
Fixed issue with category icon when creating new category
Improved table of content widget (beta)

= 2.1.0 =

Rebased versioning for new Hero Themes Version Control (HTVC)
Change textdomain hook
Added TOC widget
Fix for breadcrumbs in deep categories

= 2.0.8 =

Improved article and category ordering UI
Fixed bugs in demo installer

= 2.0.7 =

Added analytics core
Added article sorting

= 2.0.6 =

Display subcategories in parent category when option to hide in home/archive selected
Removed some legacy code

= 2.0.5 =

Category listing hotfix

= 2.0.4 =

Textdomain fix

= 2.0.3 =

Removed advanced validation for slugs to allow for more flexible permalink structure

= 2.0.2 =

Fixed issue with CMB2 activation resulting in invalid header error


= 2.0.1 =

Packaged voting module

= 2.0 =

Introduced new templating structure
Added search widget
Numerous bug fixes and coding enhancements
New helper functions
New styling options

= 1.4 =

Separated voting logic from knowledge base
Added welcome page
Added demo installer
Added auto updater functionality
Fixes and improvements for php and security issues
Updated Redux framework
Improved styling for theme compatibility
Improved title and SEO functionality
Improved general theme compatibility
Refined options UI
Various bug fixes and tweaks

= 1.3 =

Voting option improvements
Adding WPML support for knowledge base homepage
Fix for search placeholder when used with WPML
Updated translation strings
Added namespacing to some common namespacing functions
Fixed issue with kb as homepage not displaying posts in correct order
Updated namespacing on show all tag
Removed vote this comment text
Improved view count upgrade functionality
Fixed bug with subcategory article markup being displayed when there are no articles to display
Updated options wording
Added data attribute for category description
Updated HTML
Updating widget descriptions - make more consistent

= 1.2 =

Added HT Knowledge Base Archive dummy page
Article views visible in Knowledge Base post lists on backend
Added ability to set view count and usefulness manually
Added reset votes option
Article tag support
Added custom field support
Improved option to display number of articles in category or tag
Improved title output text
Added link to display remaining articles in category
Fixed voting option on individual articles
Fixed homepage option inconsistencies
Updated some translation texts to improve i18n support
Fixed display comments option for Knowledgebase


= 1.1 =

Removed ht_kb_homepage requirement (implementing themes must implement by default and declare support with ht_knowledge_base_templates)
Added loads of options for sorting and categorizing articles
Added rating indicator at various locations
Centralized display logic for plugin and supporting themes
Enhanced search and live search
Fixed breadcrumbs
Fixed page titles
Added options for archive display
Fixed where meta displays
Added support for video and standard post formats
Various other bug fixes, tweaks and enhancements

= 1.0 =

Initial release.

== Upgrade Notice ==

= 2.7.0 =

Redux has been removed to improve compatibility issues with some themes, some options
may need to be reset/saved on first activation. Searching additional post types is 
now performed with the hkb_search_post_types filter

= 2.3.0 =

As always, please ensure you backup your configuration and database before upgrading
View counts are converted after upgrade


== Developer Notes ==

For using theme templates, add support for ht_knowledge_base_templates
For category icon support add support for ht_kb_category_icons
For category color support add support for ht_kb_category_colors

== Actions and Filters == 

*ht_kb actions
ht_kb_exit_redirect - $redirect
ht_kb_end_article - null
ht_kb_activate - $network_activate
ht_kb_activate_license - $license_data
ht_kb_deactivate_license - $license_data
ht_kb_check_license - $license_data
ht_voting_add_vote_comment_action -  $comment, $vote, $post_id

*visits and feedback filters
ht_kb_record_user_search - true, $user_id, $search_data
ht_kb_record_user_visit - true,  $user_id, $object_id

*control filters
ht_kb_exit_redirect_url
ht_kb_cpt_slug
ht_kb_cat_slug
ht_kb_tag_slug
ht_knowledge_base_custom_title
hk_kb_comments_template
hkb_the_excerpt
stop_ht_knowledge_base_custom_content

*option filters
hkb_show_knowledgebase_search
hkb_archive_columns
hkb_archive_columns_string
hkb_archive_display_subcategories
hkb_archive_display_subcategory_count
hkb_archive_display_subcategory_articles
hkb_archive_hide_empty_categories
hkb_get_knowledgebase_searchbox_placeholder_text
hkb_show_knowledgebase_breadcrumbs
hkb_show_usefulness_display
hkb_show_viewcount_display
hkb_show_comments_display
hkb_show_related_articles
hkb_show_search_excerpt
hkb_show_realted_rating
hkb_focus_on_search_box
hkb_category_articles
hkb_get_custom_styles_css
hkb_custom_styles_sitewide
hkb_kb_search_sitewide

*settings section filters
hkb_add_general_settings_section
hkb_add_archive_settings_section
hkb_add_article_settings_section
hkb_add_search_settings_section
hkb_add_slugs_settings_section
hkb_add_customstyles_settings_section
hkb_add_articlefeedback_settings_section
hkb_add_transfers_settings_section
hkb_add_license_settings_section
-plus individual options

*search filters
hkb_search_post_types
