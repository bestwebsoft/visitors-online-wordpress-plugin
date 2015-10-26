=== Visitors Online by BestWebSoft ===
Contributors: bestwebsoft
Donate link: http://bestwebsoft.com/donate/
Tags: a bot, add User Online plugin, add users online counter, add visitors online counter, add visitors counter, best visitors counter, best visitors online counter, best visitors online wp counter, bot, bots, count, counte, conte, counter, count visitors, count visitors plugin, count visitors wp plugin, count users plugin, free counter, free users counter, free visitors counter, free visitors online counter, free visitors online wp plugin, gests, guests, guests online, guestscount, guestsonline, online, online guests plugin, simple counter plugin, simple visitors counter plugin, user, user online, useronline, users, users online, usersonline, VisitorOnline, visitors online, visitors online plugin, who online, widget, wp plugin, wp visitors counter, wp visitors online counter, wp visitors online, wp visitors online plugin, wordpress plugin, wordpress visitors counter, wordpress visitors online counter, wordpress visitors online, wordpress visitors online plugin
Requires at least: 3.8
Tested up to: 4.3.1
Stable tag: 0.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Plugin allows to see how many users, guests and bots are online on the website. Also, you can see statistics on the highest number of visits.

== Description ==

This plugin allows you to see the number of visitors on the site. You can set the time when a visitor is considered being online on the plugin settings page. The plugin provides the ability to display statistics on countries and browsers for the day with the highest number of visits. 
All visitors are identified as registered users, guests or bots. You can also see the day with the highest number of visits. Information about visitors can be displayed using a widget or shortcode. Also, it can be seen on the Dashboard.

http://www.youtube.com/watch?v=7e6LzyRzxwA

<a href="http://www.youtube.com/watch?v=izPS7Tbgxqg" target="_blank">Visitors Online by BestWebSoft Video instruction on Installation</a>

<a href="http://wordpress.org/plugins/visitors-online/faq/" target="_blank">Visitors Online by BestWebSoft FAQ</a>

<a href="http://support.bestwebsoft.com" target="_blank">Visitors Online by BestWebSoft Support</a>

<a href="http://bestwebsoft.com/products/visitors-online/?k=a58d73e5dee0c701959b47ea355c6e5b" target="_blank">Upgrade to Visitors Online Pro by BestWebSoft </a>

= Features =

* Display information using a widget or shortcode
* Displays information on the Dashboard
* Set the time when the visitor is considered being online without making any actions
* Clear statistics

= How It Works = 

- the user will be displayed online, if he/she had left the site, but the time when the user is considered being online is not yet passed. 
- to define the user`s country, you will need to download the file according to the instruction <a href="https://docs.google.com/document/d/1sxxeDleJdPS8HvRdYwYSABQ586t1s-Z8r6wy55iXJCM/edit" target="_blank">https://docs.google.com/document/d/1sxxeDleJdPS8HvRdYwYSABQ586t1s-Z8r6wy55iXJCM/edit</a>
- if the number of visits from different countries is the same, the plugin will display several countries, but not more than three;
- if the number of visits from different browsers is the same, the plugin will display several browsers, but not more than three

= Translation =

* Russian (ru_RU)
* Ukrainian (uk)

If you would like to create your own language pack or update the existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text of PO and MO files</a> to <a href="http://support.bestwebsoft.com" target="_blank">BestWebSoft</a>, and we'll add it to the plugin. You can download the latest version of the program for working with PO and MO files <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to contact us. Please note that we accept requests in English only. All messages in another languages won't be accepted.

If you notice any bugs in the plugin's work, you can notify us about them and we'll investigate and fix the issue then. Your request should contain website URL, issues description and WordPress admin panel credentials.
Moreover, we can customize the plugin according to your requirements. It's a paid service (as a rule it costs $40, but the price can vary depending on the amount of the necessary changes and their complexity). Please note that we could also include this or that feature (developed for you) in the next release and share with the other users then.
We can fix some things for free for the users who provide a translation of our plugin into their native language (this should be a new translation of a certain plugin, you can check available translations on the official plugin page).

== Installation == 

1. Upload `visitors-online` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin using the 'Plugins' menu in your WordPress admin panel.
3. You can adjust the necessary settings using your WordPress admin panel in "BWS Plugins" > "Visitors Online".
4. Create a page or a post and insert the short-code [vstrsnln_info] into the text.
5. Add a widget Visitors Online to the Sidebar column.

View a PDF version of <a href="https://docs.google.com/document/d/1Jhr5kJN56Bkbo4T1N_zWBR1nLfk6j0Lw5av1326KnIM/edit" target="_blank">Step-by-step Instruction on Visitors Online Installation</a>

http://www.youtube.com/watch?v=izPS7Tbgxqg

== Frequently Asked Questions ==

= I get "Not enough rights to import from the GeoIPCountryWhois.csv file" error. What shall I do? =

You should set rights 755 to the folders wp-content, plugins, visitors-online and 644 to the GeoIPCountryWhois.csv file. In such case the plugin’s script will have enough rights to upload the file. Here is some useful information for you <a href="http://codex.wordpress.org/Changing_File_Permissions#Shared_Hosting_with_suexec" target="_blank">http://codex.wordpress.org/Changing_File_Permissions#Shared_Hosting_with_suexec</a>.

= Where can I find statistics? =

You can see statistics on the admin panel, in any place of a post (if using shortcode), or in a widget (if this widget is added to the sidebar).

= Why do the number of users online displayed in statistics is greater, than it actually is? =

The user will be displayed online, if he/she had left the site, but the time when the user is considered being online is not yet passed.

= How bots are defined? =

The plugin receives data from the server variable $_SERVER['HTTP_USER_AGENT'], and searches the resulting value in its list of the most common bots. Once a match was found, the visitor is considered being a bot.

= Why the information about the day with the maximum number of visits does not match the number of actual visitors? =

The plugin counts the number of the site visitors during the day. If the same user visits the site 5 times during the day, the plugin see 5 visits, but not one. The visit means that a user enters the site or console as a guest user or bot, and stays within the time set on the plugin settings page. (The time period when the user is online, without making any actions).

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed on our forum yet (<a href="http://support.bestwebsoft.com" target="_blank">http://support.bestwebsoft.com</a>). If not, please provide the following data along with the description of your problem:

1. the link to the page, on which the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. your system status report. Please read more here: <a href="https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit" target="_blank">Instuction on System Status</a> 

== Screenshots ==

1. Plugin Settings page.
2. Displaying Visitors Online on the Dashboard.
3. Displaying Visitors Online in the Sidebar on your WordPress website.

== Changelog ==

= V0.4 - 26.10.2015 =
* NEW : A button for Visitors Online shortcode inserting to the content was added.
* Bugfix : We fixed SQL injection vulnerability.
* Bugfix : Widget displaying was fixed.

= V0.3 - 02.09.2015 = 
* Update : We updated all functionality for wordpress 4.3.

= V0.2 - 03.07.2015 = 
* Update : The Ukrainian language file is updated.
* Update : BWS plugins section is updated.

= V0.1 - 25.05.2015 = 
* Bugfix : The code refactoring was performed.
* NEW : Added detection of the country.

== Upgrade Notice ==

= V0.4 = 
A button for Visitors Online shortcode inserting to the content was added. We fixed SQL injection vulnerability. Widget displaying was fixed.

= V0.3 = 
We updated all functionality for wordpress 4.3.

= V0.2 =
The Ukrainian language file is updated
BWS plugins section is updated.

= V0.1 =
The code refactoring was performed. Added detection of the country
