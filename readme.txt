=== Extended Categories Widget ===
Contributors: petervanderdoes
Donate link: http://blog.avirtualhome.com/wordpress-plugins/
Tags: extended, categories, widget, top categories
Requires at least: 2.3
Tested up to: 2.8
Stable tag: 2.1

The AVH Extended Categories Widget gives you two widgets for displaying categories. One is a replacement of the default category widget to allow for greater customization. The second is a Top Categories widget.

== Description ==

The AVH Extended Categories Widget gives you two widgets for displaying categories.

1. Replacement of the default category widget to allow for greater customization.

1. A top categories widget. Shows the top X categories. This requires WordPress 2.8 or higher.

The replacement widget gives you the following customizable options:

* Title of the widget.
* Display as List or Dropdown.
* Show number of posts (Count) after the category.
* Hide empty categories.
* Show categories hierarchical.
* Show categories up to a certain depth. (Requires WordPress 2.8 or higher).
* Sort by ID, Name,Count.
* Sort ascending or descending.
* Show RSS link after the category as text or image.
* Select which categories to show. (Requires WordPress 2.5.1 or higher).

The Top Categories widget gives you the following customizable options:

* Title of the widget.
* How many categories to show.
* Display as List or Dropdown.
* Show number of posts (Count) after the category.
* Sort by ID, Name,Count.
* Sort ascending or descending.
* Show RSS link after the category as text or image.
* Select which categories to show. (Requires WordPress 2.5.1 or higher).

== Installation ==

The Extended Categories Widget can be installed in 3 easy steps:

1. Unzip the extended-categories-widget archive and put the directory "extended-categories-widget" into your "plugins" folder (wp-content/plugins/).

1. Activate the plugin

1. Go to the Presentation/Appearance->Widgets page and drag the widget into the sidebar of your choice. Configuration of the widget is done like all other widgets.


== Frequently Asked Questions ==

= What about support? =
I created a support site at http://forums.avirtualhome.com where you can ask questions or request features.

= Depth selection =
Starting with version 2.0 and WordPress 2.8 you can select how many levels deep you want to show your categories. This option only works when you select Show Hierarchy as well.

Here is how it works: Say you have 5 top level categories and each top level has a number of children. You could manually select all the Top Level categories you want to show but now you can do the following:
You select to display all categories, select to Show hierarchy and select how many levels you want to show, in this case Toplevel only.

== Screenshots ==
None

== Changelog ==

= Version 2.1 =
* RFC: The path for the RSS image can be URI.

= Version 2.0.3 =
* Bugfix: When selecting hierarchy and showing of all levels, the hierarchy wouldn't be shown.

= Version 2.0.2 =
* Top categories widget caused error in PHP4.

= Version 2.0.1 =
* Reported problem with calling a class by self ()

= Version 2.0 =
* Updated for WordPress 2.8. Unlimited amount of Extended Categories widgets is now possible.
* In WordPress 2.8 you have the options to select depth when showing hierarchy. See FAQ for more information.
* With WordPress 2.8 there is a new widget, AVH Extended Categories Top. This will show the top categories based on amount of posts.

= Version 1.5.1 =
* Bugfix: Compatibility issue with the plugin wp-security-scan

= Version 1.5 =
* RFC: Option to show RSS feed after categories

= Version 1.4.1 =
* Bugfix: Problem when using multiple widgets with the dropdown option.

= Version 1.4 =
* Ability to have up to 9 widgets.

= Version 1.3 =
* You can select which categories to show (Requires WordPress 2.5.1 or higher).

= Version 1.2 =
* When no category or an empty category is selected the dropdown menu shows Select Category, like the default category widget.

= Version 1.1 =
* Dropdown menu didn't work. Page wasn't refreshed with selected category.

= Version 1.0 =
 * Initial version
