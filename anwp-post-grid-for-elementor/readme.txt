=== AnWP Post Grid and Post Carousel Slider for Elementor ===
Contributors:      anwppro
Donate link:       https://anwp.pro
Tags:              post grid, post slider, post carousel, posts grid, elementor
Requires at least: 5.3
Requires PHP:      7.4
Tested up to:      7.1
Stable tag:        1.4.0
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

8 post grid and post carousel slider widgets for Elementor. Filter by category, tag or author, with load more and pagination.

== Description ==
AnWP Post Grid adds 8 post grid and post carousel slider widgets to Elementor. Pick the posts, pick a layout and style the cards, all in the Elementor panel. Columns, slides and card height follow your Elementor breakpoints, so the live page looks like the editor preview on every device.

https://www.youtube.com/watch?v=yOBSJGqJU7E

= Free widgets =
* **Simple Grid**: image cards in a responsive grid - card styles, hover effects, load more and pagination.
* **Light Grid**: a compact grid - a small thumbnail beside each title and its meta, with load more and pagination.
* **Classic Grid**: the classic blog card - image, title, excerpt and meta, with load more and pagination.
* **Classic Blog**: a blog list with the image beside the excerpt, load more and pagination.
* **Hero Block**: one large featured post with smaller ones beside it - a front-page header.
* **Simple Slider**: an image card carousel with autoplay, loop, arrows and dots.
* **Classic Slider**: Classic Grid cards as a carousel - excerpt, autoplay, slide or fade.
* **Flex Slider**: a free-mode carousel - drag through the cards, with autoplay, arrows and dots.

= Features =
* Choose posts by category, tag, author, post format or post ID, or show related posts. Exclude posts, categories or authors, and limit to posts from the last N days.
* Sort by latest or oldest (published or modified), title, most commented, or most viewed (with the free Post Views Counter plugin).
* "Load more" button and pagination on 4 grid widgets.
* Custom post types: any public post type, with its own image field.
* Category colors for the category badges (background and text).
* Post icons: show an icon on posts of a category, tag or post format.
* Columns, slides and card height per device, following your Elementor breakpoints (custom widths too). Columns and slides also take their own values on extra devices like Laptop or Tablet Extra.
* Template system: override any layout from your theme.
* Works with most themes out of the box. RTL support.

= AnWP Post Grid Premium =
Premium is an add-on: install it next to this plugin and you get 8 more widgets and 5 more features, with all 8 free widgets still there. The Premium widgets work like the free ones - the same "Posts to Show", category, tag, author and related-posts options, the same Elementor breakpoints - and they show up in the panel when you search "anwp".

**8 more widgets**

* **Hex Grid** (NEW in Premium 1.1.0): posts as interlocking hexagon tiles - responsive columns, load more, pagination and the header filter.
* **News Ticker**: a breaking-news headline bar - a continuous marquee, or one post at a time sliding sideways or up.
* **Card Slider**: a card carousel with hover effects and autoplay.
* **Hero Slider**: a full-width slider with a Ken Burns effect and post navigation.
* **Mosaic Slider**: tall cards and stacked pairs mixed into a sliding mosaic, in 3 styles.
* **Scale Grid**: the post under the mouse grows and shows its excerpt - the others stay grayscale.
* **Simple Ratio Grid**: cards locked to the image aspect ratio you pick, with load more and pagination.
* **Parallax Grid**: 3D tilt cards that follow the mouse, with a moving highlight and shadow.

**5 more features**

* **Header Category Filter**: category tabs above a grid - visitors switch categories without a page reload. Works on Simple Grid, Light Grid, Classic Grid, Classic Blog, Hex Grid and Simple Ratio Grid, and tabs that don't fit fold into a "more" menu.
* **Advanced Pagination**: page numbers with previous / next buttons, ellipsis and your own labels, on the 4 free grids and Hex Grid.
* **Custom Taxonomy**: filter any of the 16 widgets by terms from a custom taxonomy, not only categories and tags - and show those terms in place of the category label.
* **Taxonomy Redirects**: send a category or tag archive to a page of your choice (301).
* **Widget Blocks**: design a block with Elementor and show it in any sidebar or footer widget area.

Every plan covers 3 sites and comes with a 7-day free trial and a 14-day refund guarantee. Screenshots, plans and prices: [AnWP Post Grid Premium widgets](https://anwp.pro/anwp-post-grid-for-elementor-premium/). Try them live on the [Premium demo site](https://grid.anwp.pro/premium-demo/).

= Links and documentation =
* [Demo site](https://grid.anwp.pro/)
* [Documentation and community forum](https://anwppro.userecho.com/communities/50-anwp-post-grid-for-elementor)
* [Changelog](https://anwppro.userecho.com/knowledge-bases/52-anwp-post-grid-for-elementor-changelog/categories/131-basic-version/articles)

= Requirements =
* WordPress 5.3 or newer
* PHP 7.4 or newer
* [Elementor](https://wordpress.org/plugins/elementor/) 3.5.0 or newer

== Installation ==
1. Install and activate [Elementor](https://wordpress.org/plugins/elementor/), if you haven't yet.
1. Go to Plugins > Add New Plugin and search for "AnWP Post Grid".
1. Click Install Now, then Activate.
1. Open a page with Elementor, type "anwp" in the widget search box and drag a widget onto the page.

== Frequently Asked Questions ==

= Will "AnWP Post Grid for Elementor" plugin work with my theme? =

Yes! The plugin works with most themes out of the box. If you have any CSS styling issues feel free to ask for help.

= Does it work with Elementor 4? =

Yes. AnWP Post Grid is tested with Elementor 4.3 and needs Elementor 3.5.0 or newer. Its widgets are regular Elementor widgets: type "anwp" in the widget search box to find them.

= Can I show custom post types? =

Yes, any public post type. Go to AnWP Post Grid > Settings > Custom Post Type (CPT), click "Add CPT", then pick the post type and the meta field that holds its image (an image ID or URL - most post types use `_thumbnail_id`, the featured image). The post type then shows up in each widget's "Source" option.

= Can I change the widget HTML? =

Yes. Copy a file from `wp-content/plugins/anwp-post-grid-for-elementor/templates/` to `yourtheme/anwp-post-grid/`, keep the same subfolder (for example `teaser/teaser--a.php`) and edit the copy. A child theme works too. Your copy survives plugin updates, but check the changelog after an update in case the original template changed.

= Do I need this plugin to use Premium? =

Yes. Premium adds widgets and features to AnWP Post Grid, so both need to be installed and active. Update them together: Premium 1.1.0 needs AnWP Post Grid 1.4.0 or newer, and wp-admin shows a notice if one of them is too old for the other.

= How do I remove the space around the grid? =

The space comes from the Elementor layout around the widget, not from the widget. In a container, select it and set Advanced > Layout > Padding to 0 (the default for every container is in Site Settings > Layout > Container Padding). In an older section, set Layout > Columns Gap to "No Gap".

= "Load more", pagination or the header filter don't work for visitors =

These features load posts through the WordPress REST API, under the `anwp-pg/v1` namespace, and they don't need the visitor to be logged in. Some security plugins block the REST API for logged-out visitors. If yours does, add `anwp-pg/v1` to its list of allowed REST routes (often called "whitelist", "allowlist" or "exceptions").

== Screenshots ==

1. Simple Grid - image cards in a responsive grid, with load more and pagination
2. Classic Grid - image, title, meta and excerpt on every card
3. Hero Block - one large featured post with smaller ones beside it
4. Classic Blog - a blog list with the image beside the excerpt
5. Flex Slider - a drag-through carousel with autoplay, arrows and dots (one of 3 free sliders)
6. Type "anwp" in the Elementor panel to find the 8 free widgets - tiles marked PRO need the separate Premium plugin
7. Query options - filter by category, tag, author or post format, exclude posts, set an offset or show only recent posts
8. Premium (separate plugin): Hex Grid - posts as interlocking hexagon tiles

== Changelog ==

= 1.4.0 - 2026-10-29 =
* important: if you use AnWP Post Grid Premium, update it to 1.1.0 at the same time
* important: requires PHP 7.4 or higher (was 5.6) and Elementor 3.5.0 or higher (was 3.1.0) - WordPress keeps sites on older PHP on 1.3.4
* changed: grid columns, slides and card height now follow your Elementor breakpoints (Site Settings > Layout > Breakpoints), including custom widths - columns and slides also take their own values on extra devices like Laptop or Tablet Extra
* changed: with the default breakpoints, screens 576-767px wide now use the Mobile value (was Tablet), and 769-1024px sliders and 992-1024px grids use Tablet (was Desktop) - the page now matches the editor preview
* changed: with Elementor's Optimized Markup on, widgets no longer print the inner "elementor-widget-container" box (same as Elementor's own widgets) - custom CSS that targets it needs updating
* fixed: card height now applies exactly as typed - values off the 10px step (like 255) or under 150 were ignored before (Classic Grid and Classic Slider keep their 150px minimum)
* fixed: in the Elementor editor, an untouched grid or slider showed its Desktop columns on Tablet and Mobile
* note: theme copies of the plugin templates (yourtheme/anwp-post-grid/) keep working and follow the new breakpoints, even if they hardcode column classes
* changed: "Published in Last days" now filters posts in every "Posts to Show" order except Custom (before, it only worked with Oldest, Most commented and Most viewed)
* fixed: Classic Grid showed the video/gallery icon and the custom post icon on top of each other - the custom post icon now wins
* fixed: Light Grid "Post Card Background Color on Hover" had no effect with Style F
* fixed: Simple, Light and Classic Grid and Classic Blog with "load more" crashed on PHP 8 when the "Posts Offset" field was left empty
* improved: readable (unminified) copy of the settings page script and its source code are now included
* improved: the post ID selector in the Elementor editor uses the browser's native dialog (removed the Modaal library)
* improved: new Welcome page (AnWP Post Grid > Welcome) - all 16 widgets with screenshots and demo links, a quick start and what's new. After a feature update (like 1.4 to 1.5), a one-time notice links to it

= 1.3.4 - 2026-10-17 =
* important: if you use AnWP Post Grid Premium, update it to 1.0.5 at the same time - older Premium versions lose "load more" and pagination in Premium widgets
* improved: security and stability hardening
* improved: code quality and performance
* fixed: category background and text colors had no effect in Flex Slider

= 1.3.3 - 2025-01-09 =
* added: option to change Category Text color used in AnWP Grid widget

= 1.3.2 - 2024-12-17 =
* fixed: slider problem in Elementor 3.26

= 1.3.1 - 2024-12-17 =
* fixed: Schemes\Typography error in Elementor 3.26

= 1.3.0 - 2024-05-16 =
* changed: increased the minimal Elementor version to 3.5.0
* added: new option "Border" (style, color, width) in all widgets
* added: "Show Author" option in Classic Grid & Classic Slider

= 1.2.1 - 2024-02-12 =
* fixed: Light Grid image has incorrect proportions in some situations
* fixed: removed some deprecation code

= 1.2.0 - 2023-06-30 =
* added: excerpt Typography controls in "Classic Grid" and "Classic Slider"
* fixed: some slides are not visible (CSS conflict with Elementor Pro)

= 1.1.0 - 2023-03-12 =
* added: support for Swiper v8.4 (Experiments and Features)
* added: sort by Title option
* improved: minor styles improvements

= 1.0.0 - 2022-11-06 =
* fixed: slider arrows not visible on "Inline Font Icons" experimental feature active
* fixed: category color picker doesn't work in WP6.1
* minor improvements

= 0.9.1 - 2022-08-16 =
* minor improvements

= 0.9.0 - 2022-04-03 =
* removed: IE support (CSS styles)
* added: option "Image Rendering". Since v0.9.0 <img> tag is used (better support for image lazy load plugins)
* added: new widget "Flex Slider" (with initial image aspect ratio)
* added: new option "Continuous loop mode" in "Simple Slider" and "Classic Slider"
* added: possibility to sort by latest and oldest modified date
* added: Custom Post Type support

= 0.8.7 - 2022-03-15 =
* fixed: category filter disappears with installed Elementor Pro 3.6
* fixed: Swiper slider conflict with some plugins

= 0.8.6 - 2021-12-17 =
* fixed: Slider doesn't work with active "Improve Asset Loading" in Elementor v3.5

= 0.8.5 - 2021-10-19 =
* fixed: rendering author name
* added: Category ordering in Post Card
* added: option to Enable Mutation Observer. Use it to fix slider rendering in hidden areas on initialization (e.g.: tabs)
* fixed: not working Post Card Height (px) for mobile and tablet with "Load More" and "Pagination"
* added: new excerpt options (number of words, allow HTML, excerpt source) in Classic Blog, Classic Grid, and Classic Slider

= 0.8.4 - 2021-07-15 =
* added: related posts option in Query
* added: Hero Block fallback query option (when posts less than 5)

= 0.8.3 - 2021-05-05 =
* added: new special IDs Selector for posts, categories, tags, authors
* added: possibility to set custom Post Card Height for mobile, tablet, and desktop
* added: new "Exclude by Author" argument in Query section
* added: new option to open links on new tab ("AnWP Post Grid" >> "Settings")

= 0.8.2 - 2021-03-26 =
* added: 'Category Text Color' option in Simple Slider, Simple Grid and Hero Block
* added: 'Post Title Color' and 'Post Meta Color' options in Light Grid
* added: 'Category Text Color', 'Post Excerpt Text Color', and 'Post Meta Text Color' options in Classic Grid, Classic Slider, and Classic Blog

= 0.8.1 - 2021-03-06 =
* fixed: saving plugin settings in some situation

= 0.8.0 - 2021-03-06 =
* added: new option "Default Featured Image" in "AnWP Post Grid" >> "Settings"
* added: custom "Post Icons" option in "AnWP Post Grid" >> "Settings"
* fixed: Swiper (slider) is loaded properly when Elementor's "Improved Asset Loading" is set to Active
* improved: prefixed grid CSS classes to prevent conflicts with some themes
* removed: default video and gallery post format icons. Use more powerful new "Post Icons" options.

= 0.7.1 - 2020-12-13 =
* added: Settings: category color picker option to set initial color
* added: Category limit option in all widgets (possibility to show more than one category)

= 0.7.0 - 2020-11-15 =
* minor fixes
* introduced premium version

= 0.6.5 - 2020-10-28 =
* added: option to choose card hover effect (darken & scale or only darken) in "Hero Block", "Classic Grid", "Simple Grid", "Light Grid" (section "Styles & Layout")
* fixed: category background color is not applied in "Hero Block", "Simple Slider", "Classic Slider"
* fixed: incorrect vertical-align of the slider arrows in "Simple Slider" in some cases

= 0.6.4 - 2020-10-16 =
* added: pagination in "Classic Grid", "Classic Blog", "Simple Grid" and "Light Grid"
* added: a new option "Exclude by Category" in posts query

= 0.6.3 - 2020-08-18 =
* added new options in Classic Blog: post title color, post meta vertical margin, post meta background color, post image width (1/2 or 1/3)
* added new options in Classic Grid and Classic Slider: post title color, text align, post meta vertical margin, post meta background color, "read more" button

= 0.6.2 - 2020-07-03 =
* added new Classic Blog widget
* added widget header option to the most widgets

= 0.6.1 - 2020-05-29 =
* added new Hero Block widget

= 0.6.0 - 2020-05-20 =
* added 2 post carousel sliders

= 0.5.2 - 2020-05-03 =
* added "load more" button

= 0.5.1 - 2020-04-14 =
* fixed performance problem on sites with large amount of posts

= 0.5.0 - 2020-04-14 =
* Initial public release

