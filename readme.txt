=== Visitors Online by BestWebSoft ===
Contributors: bestwebsoft
Donate link: https://bestwebsoft.com/donate/
Tags: visitors online, visitors online plugin, count visitor, guests, guests counter, counter, visitors counter, add visitors counter, count, add guests counter, users online, guests online
Requires at least: 5.6
Tested up to: 6.4.3
Stable tag: 1.1.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Display live count of online visitors who are currently browsing your WordPress website.

== Description ==

Visitors Online plugin is a helpful tool which displays your website visitors count on Wordpress admin dashboard or as a widget in the front-end.
Customize and manage your visitor statistic list, see how many visitors are on your website, and the highest level of visits.

Start tracking your live traffic today!

[View Demo](https://bestwebsoft.com/demo-visitors-online-for-wordpress/?ref=readme)

https://www.youtube.com/watch?v=7e6LzyRzxwA

= Free Features =

* Automatically display visitor statistics on your dashboard
* Add visitor statistics to widgets
* Set the time when the visitor is considered being online without making any actions
* Customize the structure for data input
* Reset the statistics table
* Add custom code via plugin settings page
* Compatible with latest WordPress version
* Incredibly simple settings for fast setup without modifying code
* Detailed step-by-step documentation and videos
* Multilingual and RTL ready

> **Pro Features**
>
> All features from Free version included plus:
>
> * Automatically download and update list of countries and IP addresses
> * Display detailed user information
> * Displays online users
> * Get answer to your question within one business day ([Support Policy](https://bestwebsoft.com/support-policy/))
>
> [Upgrade to Pro Now](https://bestwebsoft.com/products/wordpress/plugins/visitors-online/?k=a58d73e5dee0c701959b47ea355c6e5b)

If you have a feature suggestion or idea you'd like to see in the plugin, we'd love to hear about it! [Suggest a Feature](https://support.bestwebsoft.com/hc/en-us/requests/new)

= Documentation & Videos =

* [[Doc] User Guide](https://docs.google.com/document/d/1FaTnRsYs64adPiRz_REGH9u0pOPz2flCL4gi49qdfaw)
* [[Doc] Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/)
* [[Doc] Purchase](https://docs.google.com/document/d/1EUdBVvnm7IHZ6y0DNyldZypUQKpB8UVPToSc_LdOYQI/)
* [[Video] Installation Instruction](https://www.youtube.com/watch?v=izPS7Tbgxqg)

= Help & Support =

Visit our Help Center if you have any questions, our friendly Support Team is happy to help — <https://support.bestwebsoft.com/>

= Translation =

* Arabic (ar) (thanks to [Said Moulla](mailto:support@saidmoulla.com), www.SaidMoulla.com)
* Czech (cs_CZ) (thanks to [PaMaDeSSoft](mailto:info@pamadessoft.cz), www.pamadessoft.cz)
* Italian (it_IT) (thanks to [Dromar](mailto:dromar030186@gmail.com))
* Russian (ru_RU)
* Ukrainian (uk)

Some of these translations are not complete. We are constantly adding new features which should be translated. If you would like to create your own language pack or update the existing one, you can send [the text of PO and MO files](https://codex.wordpress.org/Translating_WordPress) to [BestWebSoft](https://support.bestwebsoft.com/hc/en-us/requests/new) and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO [files Poedit](https://www.poedit.net/download.php).

= Recommended Plugins =

* [Updater](https://bestwebsoft.com/products/wordpress/plugins/updater/?k=c2bb0350098ca869742b01301148f8f8) - Automatically check and update WordPress website core with all installed plugins and themes to the latest versions.

== Installation ==

1. Upload `visitors-online` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin using the 'Plugins' menu in your WordPress admin panel.
3. You can adjust the necessary settings using your WordPress admin panel in "Visitors Online".
4. Create a page or a post and insert the short-code [vstrsnln_info] into the text.
5. Add a widget Visitors Online to the Sidebar column.

[View a Step-by-step Instruction on Visitors Online Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/)

https://www.youtube.com/watch?v=izPS7Tbgxqg

== Frequently Asked Questions ==

= How It Works =

- the user will be displayed online, if he/she had left the site, but the time when the user is considered being online is not yet passed.
- to define the user`s country, you will need to download the file according to the instruction <https://docs.google.com/document/d/1sxxeDleJdPS8HvRdYwYSABQ586t1s-Z8r6wy55iXJCM/>
- if the number of visits from different countries is the same, the plugin will display several countries, but not more than three;
- if the number of visits from different browsers is the same, the plugin will display several browsers, but not more than three

= I get "Not enough rights to import from the IpToCountry.csv file" error. What shall I do? =

You should set rights 755 to the folders wp-content, plugins, visitors-online and 644 to the IpToCountry.csv file. In such case the plugin’s script will have enough rights to upload the file. Here is some useful information for you <https://codex.wordpress.org/Changing_File_Permissions#Shared_Hosting_with_suexec>.

= Where can I find statistics? =

You can see statistics on the admin panel, in any place of a post (if using shortcode), or in a widget (if this widget is added to the sidebar).

= Why do the number of users online displayed in statistics is greater, than it actually is? =

The user will be displayed online if he/she had left the site, but the time, when the user is considered being online, is not yet passed.

= How bots are defined? =

The plugin receives data from the server variable $_SERVER['HTTP_USER_AGENT'], and searches the resulting value in its list of the most common bots. Once a match was found, the visitor is considered being a bot.

= Why the information about the day with the maximum number of visits does not match the number of actual visitors? =

The plugin counts the number of the site visitors during the day. If the same user visits the site 5 times during the day, the plugin sees 5 visits, but not one. The visit means that a user enters the site or console as a guest user or bot, and stays within the time set on the plugin settings page. (The time period when the user is online, without making any actions).

= How can I load GeoIP? =

1. Download the necessary file by a link <http://software77.net/geo-ip/?DL=2&x=Download>.
2. Unzip the file IpToCountry.csv.zip to a folder {your_site_folder}/wp-content/plugins/visitor-online/geolite-country.
3. Go to the settings page of the plugin Visitors Online by a link http://{your_domain}/wp-admin/admin.php?page=visitors-online.php or Admin Panel->Visitors Online.
4. Find an option "Import Database" and click "Import" button.

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<https://support.bestwebsoft.com>). If no, please provide the following data along with your problem's description:

1. the link to the page where the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste into the message your system status report. Please read more here: [Instruction on System Status](https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit)

== Screenshots ==

1. Displaying Visitors Online in the Sidebar on your WordPress website.
2. Displaying Visitors Online on the Dashboard.
3. Plugin Settings page.

== Changelog ==

= V1.1.4 - 05.03.2024 =
* NEW : Аdded shortcode that displays online users.
* Bugfix : Fixed bug with detailed information about roles in.
* Bugfix : Fixed bug with shortcodes not working in new version WP.

= V1.1.4 - 01.08.2022 =
* NEW :  A new shortcode for Visitors Online users info was added.
* Update : BWS Panel section was updated.
* Update : All functionality for WordPress 6.0.1 was updated.

= V1.1.3 - 26.04.2022 =
* Bugfix : Deactivation Feedback fix.

= V1.1.2 - 23.03.2022 =
* Update : Functionality for WordPress 5.9 was updated.
* Update : BWS Panel section was updated.

= V1.1.1 - 13.11.2020 =
* Update : BWS Panel section was updated.
* Update : Functionality for downloading and updating the country table was updated.

= V1.1.0 - 31.08.2020 =
* Update : All functionality for WordPress 5.5 was updated.
* Update : BWS Panel section was updated.
* Update : Settings page was updated.

= V1.0.9 - 04.09.2019 =
* Update: The deactivation feedback has been changed. Misleading buttons have been removed.

= V1.0.8 - 12.08.2019 =
* Update : BWS menu has been updated.
* Bugfix : The bug related with database table prefix has been fixed.

= V1.0.7 - 02.07.2019 =
* Update : The plugin settings page has been changed.
* Bugfix : The problem with import/update GeoIP databases was fixed.

= V1.0.6 - 22.01.2019 =
* PRO : Ability to display the list of users and information about each of them.
* Update : All functionality for WordPress 5.0.3 was updated.

= V1.0.5 - 03.01.2019 =
* Update : All functionality for WordPress 5.0.2 was updated.

= V1.0.4 - 19.07.2018 =
* NEW : Ability to customize plugin data output using filters has been added.

= V1.0.3 - 14.02.2018 =
* Bugfix : The problem with data displaying in "Country" fields was fixed.
* Update : All functionality for WordPress 4.9.4 was updated.

= V1.0.2 - 09.01.2018 =
* Bugfix : The problem with data displaying in "Country" and "Browser" fields was fixed.
* Bugfix : Options removal from the database is fixed.

= V1.0.1 - 10.07.2017 =
* NEW : Ability to change the data structure has been added.

= V1.0.0 - 17.04.2017 =
* Bugfix : Multiple Cross-Site Scripting (XSS) vulnerability was fixed.

= V0.9 - 14.12.2016 =
* NEW: The Arabic language file was added.

= V0.8 - 10.08.2016 =
* NEW: The Italian language file was added.
* Update : All functionality for WordPress 4.6 was updated.

= V0.7 - 28.06.2016 =
* Update : We updated all functionality for wordpress 4.5.3.

= V0.6 - 15.03.2016 =
* NEW : Ability to add custom styles.
* NEW : The Czech language file was added.

= V0.5 - 02.12.2015 =
* Bugfix : The bug with plugin menu duplicating was fixed.

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

= V1.1.4 =
* New features added.
* The compatibility with new WordPress version updated.

= V1.1.3 =
* Bugs fixed.

= V1.1.2 =
* Usability improved.

= V1.1.1 =
* Bugs fixed.

= V1.1.0 =
* The compatibility with new WordPress version updated.

= V1.0.9 =
* Usability improved.

= V1.0.8 =
* Functionality improved.
* Bugs fixed.

= V1.0.7 =
* Functionality expanded.

= V1.0.6 =
* New features added.
* The compatibility with new WordPress version updated.

= V1.0.5 =
* The compatibility with new WordPress version updated.

= V1.0.4 =
* Functionality improved.

= V1.0.3 =
* Bugs fixed.
* The compatibility with new WordPress version updated.

= V1.0.2 =
* Bugs fixed.

= V1.0.1 =
* Functionality expanded.

= V1.0.0 =
* Bugs fixed.

= V0.9 =
* New language added.

= V0.8 =
* New languages added.
* The compatibility with new WordPress version updated.

= V0.7 =
We updated all functionality for wordpress 4.5.3.

= V0.6 =
Ability to add custom styles. The Czech language file was added.

= V0.5 =
The bug with plugin menu duplicating was fixed.

= V0.4 =
A button for Visitors Online shortcode inserting to the content was added. We fixed SQL injection vulnerability. Widget displaying was fixed.

= V0.3 =
We updated all functionality for wordpress 4.3.

= V0.2 =
The Ukrainian language file is updated
BWS plugins section is updated.

= V0.1 =
The code refactoring was performed. Added detection of the country
