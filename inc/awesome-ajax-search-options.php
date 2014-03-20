<?php
global $plugin_options, $plugin_tabs;
$plugin_options = array( //array of available options
	/***************************************
	* @package Awesome_AJAX_Search
	Plugin Options
	These plugins are registered with their defaults and can be available instantly in the options menu (see in_menu and tab). They can also be available in the js instantly as well, given you edit the existing code or execute a script after mine. This is via the js globals set up with wp_localize_script().

	|*****| |*****| |*****| |*****|

	The first option is probably the best template example option.

	label => Label in menu (if in_menu is true)
	default => default value of the option
	not_null => essentially this means required, was planning on using it other places too for errors
	type => dictates how the value is validated, as well as the menu input type. IE:
	  <textarea></textarea> = "textarea"
	  <input type="text | url | email | number" /> = "text" | "url" | "email" | "int"
	  <input type="checkbox" /> = "bool" (true or false)
		more coming soon of course...
	in_menu => Display in menu? "true" or "false"
	tab => Which tab # to show in, tabs appear in order they are declared below
	hide_from_js => by default, these options are all available in the js as the Awesome_AJAX_Search_Options object.
	Options are saved as their index, ie 'plugin_activated', with the Awesome_AJAX_Search->option_prefix prefixed. IE, by default plugin_activated will show up in wp_options under "AAS_plugin_activated".

	***************************************/
	'help' => array(
		// 'label' => __('Help', 'ais'),
		'default' => '<p>' . __('If you are seeking help on using this plugin please visit my <a href="http://www.jameelbokhari.com/awesome-instant-search/">tutorial</a>.', 'ais') . '</p><p>' . __('Feel free to contact me via <a href="http://www.jameelbokhari.com/contact">my website</a>.', 'ais') . '</p><p>' . __('Check out my <a href="http://wordpress.org/plugins/jameels-dev-tools/">other plugin</a>', 'ais') . '</p>',
		'type' => 'info',
		'in_menu' => true,
		'tab' => 4,
		'description' => ''
	),
	'autocomplete' => array(
		'label' => __('Auto Complete Field', 'ais'),
		'default' => false, //default value
		'not_null' => false, //aka required, not idea for bool values!
		'type' => 'bool',   //type to verify and for input type, ie bool = checkbox
		'in_menu' => true,  //true = show in menu, else background option
		'tab' => 2,
		'hide_from_js' => false, //in case we need to hide from js localization
		'description' => __('Expirimental. This is a little buggy.', 'ais')
	),
	'plugin_activated' => array(
		'label' => __('Activate Plugin', 'ais'),
		'default' => false,
		'not_null' => false,
		'type' => 'bool',
		'in_menu' => true,
		'tab' => 0,
		'hide_from_js' => true
	),
	'useContentGif' => array(
		'label' => __('Content Loading Gif', 'ais'),
		'default' => true,
		'not_null' => false,
		'type' => 'bool',
		'in_menu' => true,
		'tab' => 1,
		'description' => __('Display loading gif in Content while results load.', 'ais')
	),
	'useSearchBarGif' => array(
		'label' => __('Search Bar Loading Gif', 'ais'),
		'default' => true,
		'not_null' => false,
		'type' => 'bool',
		'in_menu' => true,
		'tab' => 1,
		'description' => __('Display loading gif over search bar while results load.', 'ais')
	),
	'input' => array(
		'label' => __('Search Field Selector', 'ais'),
		'default' => 'input[name="s"]', //these values should work for WP 2013 Theme
		'not_null' => true,
		'type' => 'str',
		'in_menu' => true,
		'tab' => 0,
		'description' => 'Type a CSS/jQuery selector. Search field class, id, ect.'
	),
	'content' => array(
		'label' => __('Page Content', 'ais'),
		'default' => '#content', //WP 2013 Theme
		'not_null' => true,
		'type' => 'str',
		'in_menu' => true,
		'tab' => 0,
		'description' => __('Type a CSS/jQuery selector. Content that search results attach to.', 'ais')
	),
	'results' => array(
		'label' => __('Search result selector', 'ais'),
		'default' => '#content article.hentry', //WP 2013 Theme
		'not_null' => true,
		'type' => 'str',
		'in_menu' => true,
		'tab' => 0,
		'description' => __('Type a CSS/jQuery selector. This is the element inside which search results appear.', 'ais')
	),
	'urlbase' => array(
		'label' => __('Search URL', 'ais'),
		'default' => home_url() . "?s=",
		'not_null' => false,
		'type' => 'url',
		'in_menu' => true,
		'tab' => 0,
		'description' => __('Full url path to search results. Probably does not need to be changed.', 'ais')
	),
	'alsohide' => array(
		'label' => __('Hide These Elements', 'ais'),
		'default' => '#content article.hentry, #comments, #content header', //WP 2013 Theme
		'not_null' => false,
		'type' => 'textarea',
		'in_menu' => true,
		'tab' => 0,
		'description' => __('Type a CSS/jQuery selector. Elements to hide when doing an instant search. Tip: Hide comments, navigation and the current page content.', 'ais')
	),
	'speedInfo' => array(
		'default' => "<p>" . __('Times below are in milliseconds', 'ais') . "</p>",
		'type' => 'info',
		'in_menu' => true,
		'tab' => 1
	),
	'fadeOutSpeed' => array(
		'label' => __('Fade Out Speed', 'ais'),
		'default' => 149,
		'not_null' => false,
		'type' => 'int',
		'in_menu' => true,
		'tab' => 1
	),
	'fadeInSpeed' => array(
		'label' => __('Fade In Speed', 'ais'),
		'default' => 98,
		'not_null' => false,
		'type' => 'int',
		'in_menu' => true,
		'tab' => 1,
		'description' => __('Speed at which the instant search results fade in.', 'ais')
	),
	'intervalLength' => array(
		'label' => __('Delay Length', 'ais'),
		'default' => 430,
		'not_null' => false,
		'type' => 'int',
		'in_menu' => true,
		'tab' => 1,
		'description' => __('Delay before updating results.', 'ais')
	),
	'before' => array(
		'label' => __('Before Instant Results', 'ais'),
		'default' => "<header class='page-header'><h1 class='page-title'>Instant Search Results for: %%SEARCH_TERM%%</h1></header>",
		'not_null' => false,
		'type' => 'textarea',
		'in_menu' => true,
		'tab' => 0,
		'description' => sprintf(__('HTML to prepend before search results. Use %s to display the current search term.', 'ais') , '<code>%%SEARCH_TERM%%</code>')
	),
	'after' => array(
		'label' => __('After Instant Results', 'ais'),
		'default' => "",
		'not_null' => false,
		'type' => 'textarea',
		'in_menu' => true,
		'tab' => 0,
		'description' => sprintf(__('HTML to append after search results. Use %s to display the current search term.', 'ais') , '<code>%%SEARCH_TERM%%</code>')
	),
	'theme' => array(
		'label' => __('Theme Quick Settings', 'ais'),
		'default' => 'other',
		'not_null' => true,
		'type' => 'array',
		'in_menu' => true,
		'tab' => 0,
		'description' => __('Choose preconfigured settings for popular themes.', 'ais'),
		'hide_from_js' => true,
		'values' => array(
				"other" => "Custom", //these themes are in the js
				"2011" => "TwentyEleven (2011)",
				"2012" => "TwentyTwelve (2012)",
				"2013" => "TwentyThirteen (2013)"
			)
	),
	'screenmin' => array(
		'label' => __('Minimum window width', 'ais'),
		'default' => false, //default value
		'not_null' => false, //aka required, not idea for bool values!
		'type' => 'str',   //type to verify and for input type, ie bool = checkbox
		'in_menu' => true,  //true = show in menu, else background option
		'tab' => 3,
		'hide_from_js' => false, //in case we need to hide from js localization
		'description' => __('Only perform instant search when browser window is greater than or equal to this value (in pixels). Recommended value is 768 (iPad width). Set to 0 to turn this option off.', 'ais')
	),
	'debug' => array(
		'label' => __('Debug Mode', 'ais'),
		'default' => false,
		'not_null' => false,
		'type' => 'bool',
		'in_menu' => true,
		'tab' => 3,
		'description' => __('Enable this to always refresh scripts.', 'ais')
	),
	'pluginDir' => array(
		'label' => __('Plugin Directory', 'ais'),
		'default' => plugin_dir_url( __FILE__ ) . "../",
		'not_null' => true,
		'type' => 'str',
		'in_menu' => true,
		'tab' => 3,
		'description' => __('This option is used by the Awesome Instant Search javascript. It is automatically populated on plugin installation, and only needs to be changed if you move your website.', 'ais'),
		'hide_from_js' => false
	)
);
$plugin_tabs = array(
	0 => array(
		'label' => __('General Settings', 'ais'),
		'capability' => 'manage_options', //an idea, not in use
		'informational' => false
	),
	1 => array(
		'label' => __('Search Animation', 'ais'),
		'informational' => false
	),
	2 => array(
		'label' => __('Auto Complete', 'ais'),
		'informational' => false
	),
	3 => array(
		'label' => __('Advanced', 'ais'),
		'informational' => false
	),
	4 => array(
		'label' => __('Help', 'ais'),
		'informational' => true
	)
);
/* EOF */