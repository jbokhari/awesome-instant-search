=== Awesome Instant Search ===
Contributors: jameelbokhari
Donate link: http://jameelbokhari.com/
Tags: search, instant search, Google Instant search, autocomplete, AJAX, AJAX search
Requires at least: 3.0.1
Tested up to: 3.8.0
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Awesome Instant Search integrate Instant Search to ANY wordpress website.

== Description ==

Awesome Instant Search integrate Instant Search to ANY wordpress website using CSS seletors. If you are familiar with CSS already, you should have no problem setting this up. The less experienced can use [my full tutorial](http://www.jameelbokhari.com/awesome-instant-search/) to set up in 15-20 minutes. 

After installing the plugin, go to Settings->Awesome Instant Search in your admin panel. On the first tab, General Settings, you can activate the plugin, but you should probably set it up first.

If you are working with the TwentyThirteen theme, the plugin should work out of the box.
TwentyTwelve and TwentyTen can be applied instantly as well. Go to the plugin settings page described above, and under the General Settings use the dropdown next to Theme Quick Settings to select between twentythirteen, twentytwelve and twentyeleven. Again, if you are not using these themes you have to set the plugin up to fit your particular theme. Unfortunately there's not a way (that I know of) that will get this plugin working out of the box!

For the quick tutorial, see below. For those of you unfamiliar with simple HTML and CSS concepts, [see my full tutorial](http://www.jameelbokhari.com/awesome-instant-search/). 

= The Essentials =

Essentially, you need to configure three settings.

* Search Field Selector
* Page Content Selector
* Search result selector

If you know your `HTML` pretty well, here's what to do: Assign *Search Field Selector* to the class or ID of your search field(s) in your website. The default `input[name="s"]` should work for any website.

Assign *Page Content Selector* to the ID of the element you want your search results to appear in. Remember to use a period (.) or hash sign (#) just like you would with a `CSS` selector. So for example `#content`. Just like with CSS you can be more specific when you need to, e.g., `#main #content .container` and so on. Keep in mind, this content will be hidden on the current page where the search is performed.

Next, in the same fashion, assign a selector for the search results. In about 99% of websites, this is going to be the same as the content selector with .hentry added in there. For example, if you assigned `#content` as your *Page Content Selector*, you would use `#content .hentry` as your *Search Result Selector*. 

These are the three most important settings. After you get these, go ahead and test it out or play with the other settings.

= Other Settings =

**Search URL** is where the search results appear. This is your wordpress website's domain with the letters `?=s` appended to it &#8212; e.g., http://www.example.com?=s
This shouldn't need to be changed but is there just in case you ever need to change it.

**Hide These Elements** If you want additional elements to be hidden when performing a search, use this option. These elements will be revealed again when the search is cleared. This value accepts comma separated `CSS` selectors, use it to hide comments, page navigation and other extraneous elements when searching.

**Before Instant Results** is `HTML` to appear before the Instant Search results. Use the tag %%SEARCH_TERM%% to print the search term, e.g., `<h3>Search results for: "%%SEARCH_TERM%%"</h3>` might show up as `<h3>Search results for: "Contact Info"</h3>` on your page.

**Theme Quick Settings** Quickly access default settings for twentyten, twentytwelve, and twentythirteen.

= Translation =
* Español [Maria Romos with Web Hosting Hub](http://www.webhostinghub.com/).

== Installation ==

1. Upload `awesome-instant-search` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Navigate to the Settings Page
1. Navigate to the Awesome Instant Search page under settings
1. Set up settings for your theme ([see my tutorial](http://www.jameelbokhari.com/awesome-instant-search/))
1. Activate the plugin under the General Settings tab on the plugin settings page

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 1.1.2 =
* Fixed some wording.
* Completed Spanish translation for last minute features.

= 1.1.1 =
* Fixed issue with last commit, files got mixed up.
* Added feature to set minimum screen size that Awesome Instant Search requires to trigger so it can be turned off on smaller and (presumably low bandwidth) devices.

= 1.1.0 =
* He añadido la traducción español thanks to [Maria Romos with Web Hosting Hub](http://www.webhostinghub.com/index-c.html?utm_expid=31925339-46.KEGZK8A6Q3yfZW0EUfEw5Q.1). Plugin is also ready for other languages.
* Fixed bug involving the plugin saving the help info text.
* Fixed possible issue arrising when the site_url is changed (e.g., production to live). Users can now manually set the plugin directory if needed. This process may be cleaned up more in the future.
* Changed some wording to clarify settings

= 1.0.0 =
* Initial Release

== Upgrade Notice ==

= 1.1.1 =
* Verbiage updates, spanish translation

= 1.1.1 =
* Issues with last commit, finished screen size option

= 1.1.0 =
* Bugs, translation ready, new fields

= 1.0.0 =
* Initial Release
