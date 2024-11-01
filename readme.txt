=== WP Template On The Fly ===
Contributors: alimotorolla
Donate link: http://alibabaei.com/category/web-development/wordpress/plugins/wp-template-on-the-fly/
Tags: theme, template, sidebar
Requires at least: 3.0.1
Tested up to: 3.6.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create templates, override your theme's template files, create sidebars, put sidebars in your templates, apply your custom style, add plugins.

== Description ==

Create templates on the fly.

Just by some few clicks.

Override your theme's template files by your wish.

Create any number of sidebars you want.

Put sidebars in your custom created templates.

Apply your custom style to created sidebars as well as templates.

Add any number of plugins to sidebars.

And its done, yes!

You created new theme which you can edit it from 0% to 100% by only some clicks, any time, any where, and for any reason, and add any number of custom template to your theme too.

This version is just for start. There are many  extreme properties which I designed, but I need more time to implement them, be patient.

Help me make it better by your ideas as well as subscription payment!

This plugin will change the world of WordPress Theme Development!

You not agree? let see!

<strong>Documents - Tutorial - Gallery - Donate - Help & Support and .... at [alibabaei.com](http://alibabaei.com)</strong>

= Documentation =

WP : WordPress

WPTOF : WP Template On The Fly

= What is the approach? =

If you are familiar with WP, you know that it is based on <strong>Post</strong>s.

But <strong>Post</strong>s have <strong>ID</strong>, <strong>Title</strong>, <strong>Date</strong>, they can have <strong>Category</strong>s, <strong>Custom Taxonomy</strong>s, <strong>Tag</strong>s, <strong>Custom Post Type</strong>s and so on.

Then think you have created your blog with some <strong>Post</strong>s, <strong>Page</strong>s, other <strong>Custom Post Type</strong> <strong>Post</strong>s, some <strong>Category</strong>s, some <strong>Custom Taxonomy</strong>s, some <strong>Tags</strong>s, some of them are published by some <strong>Author</strong>s, in some <strong>Month</strong>s, <strong>Year</strong>s, <strong>Day</strong>s, <strong>Week</strong>s, and in continue, you have uploaded some <strong>Attachment</strong> to your blog.

What should you do if you decided to make <strong>Specific Template</strong> per <strong>Post</strong>?

In the best case you have read codex.wordpress.org articles and made a good understanding on it, then you can create some files to gain your goal.

But if you have no knowledge in PHP, WP Functions and Coding Structure and etc, what should you do?

Before you email me to do such jobs for you, I decided to do it by myself, in general purpose, and with all aspects and considerations.

<strong>Specific Template</strong> can be made based on <strong>ID</strong> of <strong>Post</strong>, <strong>Date</strong> such as <strong>Year</strong>, <strong>Month</strong> and <strong>Day</strong> of publish, the <strong>Author</strong>, the <strong>Type</strong> of <strong>Post</strong>, <strong>Tag</strong>, <strong>Category</strong>, <strong>Taxonomy</strong>, or if its an <strong>Attachment</strong>, it can be made based on <strong>Mimetype</strong>, <strong>Subtype</strong>, or <strong>Mimetype_Subtype</strong>.

It is the capability of WP, not me, not you, not any one else.

But using this capability is hard for publics, conversely, easy or less hard to programmers.

By WPTOF, you can create any <strong>Specific Template</strong> you want, for any reason, by some clicks.

No programming skill you need as well as no WP structural understanding.

Because you will not create any <strong>.php</strong> file, although you will create <strong>Template</strong>!

Yes!

<strong>All job is done as virtual.</strong>

= Process: =

you will create <strong>Template</strong>s by just select <strong>Type of Template</strong>, 

which is : <strong>Which request the Template will serve to?</strong>, 

you will save <strong>Template</strong>s in <strong>Database</strong>, 

you will apply <strong>Style</strong> to <strong>Template</strong>s by just some click and inserting some values, as well, 

you will save <strong>Style</strong> of <strong>Template</strong>s into <strong>Database</strong>, 

you will create some boxes to be displayed in <strong>Template</strong>s, 

these boxes are <strong>Sidebar</strong>s, 

(although they can be in center, top or bottom, or anywhere, the surname is remained <strong>Sidebar</strong> since past), 

any <strong>Sidebar</strong> has a unique name, 

then you will apply <strong>Style</strong> to <strong>Sidebar</strong>s by just some click and inserting some values, as well, 

you will save <strong>Style</strong> of <strong>Sidebar</strong>s into <strong>Database</strong>, 

you will sort <strong>Sidebar</strong>s by your wish in each <strong>Template</strong>, 

then you will find some <strong>Plugin</strong>s, going to <strong>Widgets</strong> page, you will assign those <strong>Plugin</strong>s to <strong>Sidebar</strong>s, 

and that's it.

= Point 1: =

Any <strong>Sidebar</strong> can be in any <strong>Template</strong>, it can be repeated multiple times in a <strong>Template</strong>, as well, in the same time, it can be in other <strong>Template</strong>s, <strong>Style</strong> of <strong>Sidebar</strong> is applied to it everywhere it is, in any <strong>Template</strong> and in any <strong>Sort</strong> number.

= Example 1: =

Create <strong>index.php</strong>, if there is no <strong>Theme</strong> and no other <strong>Template</strong>s, for any request WP will select <strong>index.php</strong>. (See WP <strong>Template</strong> Hierarchy)

Apply some <strong>Style</strong>s, such as <strong>max-width</strong>, <strong>padding</strong> and <strong>margin</strong>.

<strong>Notice:</strong> WPTOF will print a <strong>div</strong> for <strong>Template</strong> and will apply <strong>Style</strong> to it. (If you have no idea which <strong>div</strong> is, don't worry, there is no need to know!)

You can see any changes in <strong>Style</strong> after you clicked on <strong>Save Style</strong> button.

In the next step, create a <strong>Sidebar</strong> and name it <strong>Header</strong>.

Going to <strong>Template-Sidebar</strong> tab, assign new <strong>Sidebar</strong> to your new <strong>Template</strong>.

Now you will see a box added to <strong>Template</strong> box, click on new box, <strong>Active</strong> sign appeared, <strong>Style Target</strong> changed, now you can change the <strong>Default Style</strong> of new children box.

It will result a <strong>Template</strong> box which is <strong>Parent</strong>, and a <strong>Sidebar</strong> box which is <strong>Children</strong>.

In this Time you can go to <strong>Widgets</strong> page of WP and assign a plugin to it.

In this example i assumed that you want show only one <strong>Banner</strong> in your <strong>Header Sidebar</strong>.

Then you can install <strong>Image Banner Widget</strong> and put an banner image into your sidebar.

This was soooooo simple, wasn't it?!

Now you can create any number of <strong>Template</strong>s and assign this <strong>Header Sidebar</strong> to them.

In fact you created a simple <strong>Theme</strong>, but untill now, only <strong>Header</strong> part of it is complete!

= Some definitions: =

= Template: =

When you install WP, it came with default <strong>Theme</strong>.

Going to /wp-content/themes/<strong>Current Theme</strong> directory, you will see some <strong>.php</strong> files, as well as some folders and etc.

Some of those <strong>.php</strong> files are <strong>Template</strong>s.

Then, short concept, any <strong>Theme</strong> is made of <strong>Template</strong>s and other things, but <strong>Template</strong>s are basic.

Without <strong>Template</strong>s, no <strong>Theme</strong> can show posts, pages, archives or etc.

Then <strong>Template</strong> is a <strong>.php</strong> file which is related to a part of WP, and <strong>Theme</strong> is made of <strong>Template</strong>s, each for responding to the kind of visitor's requests.

When WP receives a request, which has a URL, and is sent by site visitors, it tries to find the <strong>Template</strong> provided by <strong>Theme</strong> to reply to such kind of requests, when found, WP will load <strong>Template</strong>, which is, <strong>.php</strong> file.

We will see the WP <strong>Template</strong> Hierarchy later (as well as you can see it in <strong>Template</strong> tab in WPTOF setting page, it is shown as <strong>Parent</strong> fields of <strong>Primary</strong> <strong>Template</strong>s), but now you should know that WP will search <strong>Template</strong>s in a hierarchical manner, such as a tree, for example, if a leaf not found, WP will search for parent of that leaf, if parent not found, WP will search for grand parent of leaf and so on.

At the end, if there was no <strong>Tempalte</strong> except <strong>index.php</strong>, it will be shown to the visitor.

= Point 1: =

If you want the <strong>Template</strong>s in WPTOF override their brothers in your WP installed <strong>Theme</strong>, you should <strong>Active</strong> them, if they are <strong>Inactive</strong>, they will not be considered by WP at all.

= Example 2: =

<strong>page.php</strong> is a <strong>Template</strong> to show <strong>Page</strong>s.

<strong>archive.php</strong> is a <strong>Template</strong> to show any <strong>Archive</strong>s.

<strong>category.php</strong> is a <strong>Template</strong> to show <strong>Category</strong>s.

<strong>author.php</strong> is a <strong>Template</strong> to show <strong>Author</strong>s.

= Example 3: =

If request URL is for a <strong>Category</strong>, WP will search for <strong>category.php</strong>, if it found, it will be shown, else WP will search for <strong>archive.php</strong>, in same manner, if it found it will be shown, else WP will search for <strong>index.php</strong>.

= Some used words: =

= Primary: =

These are constant <strong>Template</strong>s which I created and inserted to WPTOF database, without them WPTOF could not did the job correctly, these <strong>Template</strong>s are defined and you have no need to define them, they number is too less than number of <strong>Template</strong>s which you can define.

If you want to use them, add them <strong>Sidebar</strong>s and <strong>Active</strong> them, then they will be shown by WP.

= These <strong>Template</strong>s are: =

* index.php
* * 404.php
* * archive.php
* * * author.php
* * * category.php
* * * date.php
* * * * day.php
* * * * month.php
* * * * year.php
* * * tag.php
* * * taxonomy.php
* * comments-pupop.php
* * home.php
* * * front-page.php
* * page.php
* * paged.php
* * search.php
* * single.php
* * * attachment.php
* * * single-post.php

These are <strong>Constant</strong> <strong>Template</strong>s, so you can only add <strong>Sidebar</strong>s to them and <strong>Active</strong> them, but there are some <strong>Variable</strong> <strong>Template</strong>s which are as following:

------------------

<strong>archive-$posttype.php</strong>

[Child of <strong>archive.php</strong>]

*** The bolder part of <strong>Template</strong>'s name is <strong>Variable</strong>, but as its word shows, <strong>$posttype</strong>, it expects a <strong>Post Type</strong>, which can be <strong>Post</strong>, <strong>page</strong>, or other <strong>Custom Post Type</strong>.

If you created a <strong>Custom Post Type</strong> and want to allocate <strong>Specific Template</strong> to its <strong>Archive</strong>, create this type of <strong>Template</strong>, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Post Type</strong>s for you to select.

------------------

<strong>year-$year.php</strong>

[Child of <strong>year.php</strong>]

If you want to allocate <strong>Specific Template</strong> to the <strong>Post</strong>s of certain year, create this type of <strong>Template</strong>, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a text box for you to insert year number, in should be in 4 digit format like 2013.

------------------

<strong>month-$month.php</strong>

[Child of <strong>month.php</strong>]

If you want to allocate <strong>Specific Template</strong> to the <strong>Post</strong>s of certain month, create this type of <strong>Template</strong>, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a text box for you to insert month number, in should be in 2 digit format like 10.

------------------

<strong>day-$day.php</strong>

[Child of <strong>day.php</strong>]

If you want to allocate <strong>Specific Template</strong> to the <strong>Post</strong>s of certain day, create this type of <strong>Template</strong>, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a text box for you to insert day number, in should be in 2 digit format like 06.

------------------

<strong>taxonomy-$taxonomy.php</strong>

[Child of <strong>taxonomy.php</strong>]

If you created a <strong>Custom Taxonomy</strong> and  want to allocate <strong>Specific Template</strong> to it, create this type of <strong>Template</strong>, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Custom Taxonomy</strong>s for you to select.

------------------

<strong>taxonomy-$taxonomy-$term.php</strong>

[Child of <strong>taxonomy-$taxonomy.php</strong>]

If you created a <strong>Custom Taxonomy</strong> and  want to allocate <strong>Specific Template</strong> to certain <strong>Term</strong> of this type, create this type of <strong>Template</strong>, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Custom Taxonomy</strong>s  as well as a list of <strong>Term</strong>s from each <strong>Custom Taxonomy</strong> for you to select.

------------------

<strong>category-$id.php  |  category-$slug.php</strong>

[Child of <strong>category.php</strong>]

If you want to allocate <strong>Specific Template</strong> to certain <strong>Category</strong> by <strong>id</strong> or <strong>slug</strong>, create on of these types of <strong>Template</strong>s, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Category</strong>s for you to select.

------------------

<strong>tag-$id.php  |  tag-$slug.php</strong>

[Child of <strong>tag.php</strong>]

If you want to allocate <strong>Specific Template</strong> to certain <strong>Tag</strong> by <strong>id</strong> or <strong>slug</strong>, create on of these types of <strong>Template</strong>s, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Tag</strong>s for you to select.

------------------

<strong>page-$id.php  |  page-$slug.php</strong>

[Child of <strong>page.php</strong>]

If you want to allocate <strong>Specific Template</strong> to certain <strong>Page</strong> by <strong>id</strong> or <strong>slug</strong>, create on of these types of <strong>Template</strong>s, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Page</strong>s for you to select.

------------------

<strong>author-$id.php  |  author-$nicename.php</strong>

[Child of <strong>author.php</strong>]

If you want to allocate <strong>Specific Template</strong> to certain <strong>Author</strong> by <strong>id</strong> or <strong>nicename</strong>, create on of these type of <strong>Template</strong>s, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Author</strong>s for you to select.

------------------

<strong>single-$posttype.php</strong>

[Child of <strong>single.php</strong>]

If you want to allocate <strong>Specific Template</strong> to show single <strong>Post</strong>s of certain <strong>Post Type</strong>, create on of this type of <strong>Template</strong>s, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Post Type</strong>s for you to select.

------------------

<strong>$mimetype.php</strong>

[Child of <strong>attachment.php</strong>]

If you want to allocate <strong>Specific Template</strong> to show single <strong>Attachment</strong>s of certain <strong>Mime Type</strong>, create on of this type of <strong>Template</strong>s, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Mime Type</strong>s for you to select.

------------------

<strong>$subtype.php</strong>

[Child of <strong>attachment.php</strong>]

If you want to allocate <strong>Specific Template</strong> to show single <strong>Attachment</strong>s of certain <strong>Sub Type</strong>, create on of this type of <strong>Template</strong>s, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>Sub Type</strong>s for you to select.

------------------

<strong>$mimetype_subtype.php</strong>

[Child of <strong>attachment.php</strong>]

If you want to allocate <strong>Specific Template</strong> to show single <strong>Attachment</strong>s of certain <strong>Mime Type & Sub Type</strong>, create on of this type of <strong>Template</strong>s, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>mimetype_subtype</strong>s for you to select.

------------------

<strong>$custom.php</strong>

[Child of <strong>page.php</strong> or <strong>index.php</strong>]

If you want to define a custom <strong>Template</strong> to allocate it to any page or anything possible, create on of this type of <strong>Template</strong>s, add <strong>Sidebar</strong>s to it, and finally <strong>Active</strong> it.

I provided a list of <strong>mimetype_subtype</strong>s for you to select.

------------------

Now you know which type of <strong>Template</strong>s you can create and how.

When you created a <strong>Template</strong> from one of above <strong>Variable Template</strong>s, you will see that the <strong>Primary</strong> field of its record will be yellow image, it means your new <strong>Template</strong> is not <strong>Primary</strong>, because you created it, not me!:))

Now you know what are <strong>Active</strong>, <strong>Inactive</strong>, <strong>Primary</strong>.

<strong>Deleted</strong> is not anything, do not pay attention to it!:))

When you created your custom <strong>Template</strong>s, you should go to <strong>Sidebar</strong>s.

Here we go!

= Sidebar: =

When you install <strong>Theme</strong>s or use default <strong>Theme</strong>, certainly you would deal with <strong>Sidebar</strong>s.

You would install some of the <strong>Plugin</strong>s and put their <strong>Widget</strong>s in <strong>Sidebar</strong>s.

There is no difference between your previous <strong>Theme</strong>'s <strong>Sidebar</strong>s and newly created <strong>Sidebar</strong>s in WPTOF.

The points are:

1- WPTOF <strong>Sidebar</strong>s are dynamically <strong>Create</strong>d by you, not by your <strong>Theme</strong> developer.

2- WPTOF <strong>Sidebar</strong>s are dynamically <strong>Style</strong>d by you, not by your <strong>Theme</strong> developer.


== Installation ==

1. Go to Plugins->Add New.
2. Search for 'WP Template On The Fly'.
3. Install and Activate it.
4. Enjoy Theming on the fly.

== Frequently Asked Questions ==

= How can i use this? =

Come to my site and pay for tutorial.

== Screenshots ==

1. List of all templates, either I created and yours, these templates can be activated, then WP will use these templates instead of template files in /themes directory.
2. Tail of above list.
3. Create new template, select type of it, for example I want to create Specific Template for specific term from specific taxonomy! then I should select <strong>taxonomy-$taxonomy-$term.php</strong> from <strong>Template Type List</strong>
4. Whe I selected <strong>taxonomy-$taxonomy-$term.php</strong>, a simple form appeared by ajax, in this form i can select <strong>taxonomy</strong> and <strong>term</strong>, when i click on <strong>create</strong> button, template will be created, this template will be used by WP if i activate it.
5. Here I can <strong>Create</strong> any <strong>Sidebar</strong> or <strong>Remove</strong> them. these sidebars can be used anywhere in WP, as well, WPTOF.
6. Here I can <strong>Append</strong> any sidebar to any template, apply style to templates, apply style to sidebars, order sidebars, or deappend sidebars.
7. 2 sidebar added to a template.
8. 15 sidebar added to a template. I changed styles of sidebars so presentation changed by ajax.
9. This is how my sample template will be shown, but there is now plugins inside sidebars, only remained step is it. I should go to Widget page of WP and add any plugin to my sidebar, once I added them, they will apear in my sample tempalte. thet's it, enjoy.

== Changelog ==

= 1.1 =
* This version fixes some bugs and errors in JS and jQuery coding, so the plugin will work perfect now.

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.1 =
I am sorry if version 1.0 did not work, please upgrade so this version will fix bugs and errors and now, will work perfect.