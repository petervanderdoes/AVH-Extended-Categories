=== Extended Categories Widget ===
Contributors: petervanderdoes
Donate link: http://blog.avirtualhome.com/wordpress-plugins/
Tags: extended, categories, widget, top categories
Requires at least: 2.3
Tested up to: 2.8
Stable tag: 2.3.3

The AVH Extended Categories Widget gives you two widgets for displaying categories.
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

Translations:
* Czech - Čeština (cs_CZ) by Dirty Mind - http://dirtymind.ic.cz
* Spanish - Español (es_ES) in Launchpad

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

= I want to help and translate the plugin =
The .pot file is included with the plugin.
If you have created a language pack, or have an update of an existing one, you can send the [gettext .po and .mo files](http://codex.wordpress.org/Translating_WordPress) to me so that I can include them in future releases.
I'll keep a list of translators and their websites here in the readme.txt and on my website.

I have also setup a project in Launchpad for translating the plugin. Just visit http://bit.ly/95WyJ

== Screenshots ==
None

== Changelog ==
= Version 2.3.3 =
* Bugfix: Validation error on dropdown categories.

= Version 2.3.2 =
* Bugfix: Saving the option didn't work
* Added translation: Spanish - Español (es_ES)

= Version 2.3.1 =
* Bugfix: Undefined function

= Version 2.3 = 
* RFC: You can change the text Select Category, without editing any translation file.
* Bugfix: Selecting one parent wouldn't show it's children.
* You can now display only selected categories. This didn't work properly in an hierachical category structure.
* Added Czech translation.

= Version 2.2 =
* RFC: Internationalization of the plugin.
* Bugfix: W3 Validation Errors when using dropdown categories.
* Added option to exclude categories from displaying instead of including them. (Compliments to: Jose Luis Moya - http://www.alsur.es ) 

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
