 === Plugin Name ===
Contributors: Marty Thomas, Purlem
Tags: Personal URL, Personalized URL, PURL, Marketing
Link: http://purlem.com/
Requires at least: 2.1
Tested up to: 3.2.1 
Stable tag: 1.2.2

Personalize your blog to visitors and track results with Personalized URLs (PURLs). The Plugin Requires a Purlem Account.

== Description ==
Requires an active Purlem account, and a Linux Server with PHP5+ installed. 

The Plugin will turn your blog into a Personal URL landing page fully integrated with Purlem's marketing system.  A sample Personal URL (PURL) would be 'www.myblog.com/Joe.Smith', where 'Joe.Smith' would be variable for each person in your campaign. 

When 'Joe' visits his Personal URL, he will be redirected to a specific page in your blog. This page will not only track Joe's activity, but can also greet Joe by name and provide a pre-populated questionnaire. 


== Installation ==

1. Unzip and Upload `purlem-personalized-url` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Create a new Blog Page to use as the PURL Landing Page.
3. Under the 'Settings' menu in Wordpress, locate 'Purlem'. Insert Purlem UserID and Blog Page URL into Purlem Settings. 

== Frequently Asked Questions ==

= Is A Purlem Account Required? =

Yes. You must have an active Purlem account for the function to work. 

= How is the visitor redirected to the correct Blog Page? =

This is accomplished through the '.htaccess' file located in the root directory of the blog.
