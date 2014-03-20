<?php
/*
 Plugin Name: Awesome Instant Search
 Plugin URI: http://jameelbokhari.com
 Description: A plugin to add instant search to your website, a la Google Instant. Some assembly is required.
 Version: 1.1.2
 Author: Jameel Bokhari
 Author URI: http://jameelbokhari.com
 License: GPL2

    Copyright 2013  Jameel Bokhari  ( email : me@jameelbokhari.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/**
 * @package Awesome_AJAX_Search
 * @uses inc/awesome-ajax-search-options.php - collection of options that can be easily added and removed from plugin. Also contains information on options tabs in the admin section.
 * @todo add autocomplete based on pages, categories or menus :D
 * - preferred suggestions option - choose what pages you'd like to come up when the field is blank
 * - test that values must match up for options, prob not neccessary this version
 * - test other type checking of Awesome_AJAX_Search::update_option
 * - Newbie selector tool - preview site in window, use hover event just like google developer tools to allow newbies to select the places they want the search to work.
 * - Preview mode to use before activating plugin, like a dummie page where it's the only page active.
 */
class Awesome_AJAX_Search {
	private $options = array();
	private $ver = '1.1.2';
	private $option_prefix = 'AAS_';

	private $tabs;

	public function __construct(){
		$this->ap_action_init();
		register_activation_hook( __FILE__, array($this, 'ajax_search_activate') );
		$this->init();
	}
	public function ap_action_init(){
		load_plugin_textdomain('ais', false, dirname(plugin_basename(__FILE__)) . "/translation" );
		require_once("inc/awesome-ajax-search-options.php");
		global $plugin_options;
		global $plugin_tabs;
		$this->options = $plugin_options; //set defaults
		$this->tabs = $plugin_tabs;

	}
	public function init(){
		$plugin_activated = $this->option_prefix . 'plugin_activated';

		add_action('plugins_loaded', array($this, 'ap_action_init') );

		add_action( 'admin_menu', array($this, 'add_admin_page_to_menu') );	
		if( get_option( $plugin_activated, false ) ){
			add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts_and_styles'), 999);
		}

		add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts'), 100 );
	}

	/**
	 * Fuction to register the plugin options. This has been made to be easily extended by calling outside of the class using $extends array, which should be formatted like $plugin_options. This should be called before init()
	 * @param type array $extends 
	 * @uses $plugin_options from awesome-ajax-search-options.php
	 * @return mulitidemensional array $options
	 **/

	public function register_options(array $extends = array()){

		foreach( $extends as $option => $values ){

			$plugin_options[$option]['label'] = strval($values['label']);
			$plugin_options[$option]['default'] = $values['default'];
			$plugin_options[$option]['in_menu'] = $values['in_menu'];
			$plugin_options[$option]['not_null'] = $values['not_null'];
			$plugin_options[$option]['type'] = strval($values['type']);
			$plugin_options[$option]['tab'] = intval($values['tab']);

		}
		
		$this->options = $plugin_options;

	}
	private function update_option( $name, $value ){
		$option = $this->options[$name]; // mind boggles, code benefits
		$error;
		// if (require && empty, do default)
		if( $value == '' && isset($option['not_null']) && $option['not_null'] ){ //is it required?
			$value = $option['default'];
			return update_option($option, $value );
		}
		switch($option['type']) {
			case 'url':
			case 'email':
			case 'str':
			case 'textarea':
				$value = esc_textarea( $value );
				break;
			case 'int' :
				$value = intval($value);
				break;
			case 'bool' :
				$value = $value;
				break;
			case 'array' :
				// if value is an available option
				$value = ( in_array( $value, $option['values'] ) ) ? $value : $option['default'];
				break;
			default :
				$value = $option['default'];
				break;
		}
		$id = $this->option_prefix . $name;
		return update_option($id, $value );
	}

	private function do_tabs($current){
	    echo '<div id="icon-themes" class="icon32"><br></div>';
	    echo '<h2 class="nav-tab-wrapper">';
		$plugin_tabs = $this->tabs;
	    $tabindex = 0;
	    foreach( $plugin_tabs as $tabslug => $values ){
	    	$label = $values['label'];
	        $class = ( $tabindex == $current ) ? 'nav-tab-active' : '';
	        echo "<a class='nav-tab $class' href='options-general.php?page=awesome-instant-search-options&tab=$tabindex'>$label</a>";
	        $tabindex++;
	    }
	    echo '</h2>';
	}
	private function update_admin_options($current_tab = null){
		$msg = '';
		$affected = 0;
		extract($_POST);

		foreach ($this->options as $name => $values) {
			if ( $values['type'] == 'info' ) {
				continue;
			}
			if ( $values['in_menu'] && $values['tab'] == $current_tab ){
				if (!isset($$name) && $values['type'] == 'bool'){
					$newvalue = false;
				} else {
					$newvalue = $$name;
				}
				if ( $this->update_option($name, $newvalue) ){
					$affected++;
				}
			}

		}
		if( $affected > 0 ){
			$msg .= __("Options have been saved.", 'ais');
		} else {
			$msg .= __('No options were changed!', 'ais');
		}

		return $msg;
	}
	public function display_admin_page(){
		global $plugin_tabs;
		echo "<div class='wrap awesome-instant-search'>";

		$current_tab = ( isset( $_GET['tab'] ) ) ? intval( $_GET['tab'] ) : 0 ;
		$msg = '';
		$nonce = ( isset( $_POST['aas_nonce'] ) ) ? $_POST['aas_nonce'] : '' ;
		if( isset($_POST['save_post']) && current_user_can( 'manage_options' ) && wp_verify_nonce( $nonce, 'aas_do_save_nonce' ) ){

			// print_r($_POST);
			$msg = $this->update_admin_options($current_tab);

		}

		echo "<h2>Awesome Instant Search</h2>";

		$this->do_tabs($current_tab);

		if($msg != ''){
			echo "<div id='message' class='updated'><p>{$msg}</p></div>";
		}

		echo "<form action='' method='post'>";
		$options = $this->options;
		foreach ($options as $name => $values) {
			if( isset( $values['tab'] ) && $values['tab'] != $current_tab ){
				continue;
			}
			echo "<div class='option-block'>";
			if ( isset( $values['in_menu'] ) && $values['in_menu'] ){
				$this->display_admin_option($name);
			}
			echo "</div>";
		}
		$key = intval($current_tab);
		$tabvals = $plugin_tabs[$key];
		$hidesave = ( isset($tabvals['informational']) ) ? $tabvals['informational'] : false;
		if ( ! $hidesave ){
			echo "<input type='hidden' name='save_post' value='1' />";
			echo "<input type='hidden' name='tab' value='{$current_tab}' />";
			wp_nonce_field( 'aas_do_save_nonce', 'aas_nonce' );
			echo "<button class='button button-primary' type='submit'>" . __("Save Options") . "</button>";
		}
		echo "</form>";
		echo "</div><!-- EOF WRAP -->";
	}

	public function admin_scripts(){

		$debug = $this->option_prefix . 'debug';
		if (get_option( $debug, $default = false )){
			$version = uniqid();
		} else {
			$version = $this->ver;
		}

		wp_enqueue_style( 'awesome-ajax-search-admin-css', plugin_dir_url(__FILE__) . 'css/awesome-ajax-search-admin.css', array(), $version, $media = 'all' );
		wp_enqueue_script( 'tb-ajax-search-admin', plugin_dir_url(__FILE__) . 'js/awesome-ajax-search-admin.js', 'jquery', $version );
		$options = $this->options;
		$include = array();
		$include['prefix'] = $this->option_prefix;
		foreach($options as $name => $values){

			if ( isset($value['admin_js']) ){
				$id = $this->option_prefix . $name;
				$default = $values['default'];
				$value = get_option( $id, $default );
				$include[$name] = $value;
			}
		}		

		wp_localize_script( 
			'tb-ajax-search-admin',
			'Awesome_AJAX_Search_Options',
			$include
		);
	}

	/**
	 * Register the admin page
	 * @return type
	 */
	public function add_admin_page_to_menu(){

		$parent_slug = 'options-general.php';
		$page_title  = __('Awesome Instant Search Options', 'ais');
		$menu_title  = 'Awesome Instant Search';
		$capability  = 'manage_options';
		$menu_slug   = 'awesome-instant-search-options';
		$function    = array($this, 'display_admin_page');

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	} 
	/**
	 * Displays the input option for the admin area, just provide $name and the resto is autmagic
	 * @param string $name 
	 * @uses Awesome_AJAX_Search->options
	 * @return prints label and input
	 */
	private function display_admin_option( $name ){
		$html = '';
		$option = $this->options[$name];

		// strval may seem a little paranoid but for extension may be necessary
		$before = (isset($option['before'])) ? strval($option['before']) : '';
		$after = (isset($option['after'])) ? strval($option['after']) : '';
		$class = (isset($option['class'])) ? strval($option['class']) : '';
		$description = (isset($option['description'])) ? strval($option['description']) : '';

		$id = $this->option_prefix . $name;
		if ( isset( $option['label'] ) ){
			$label = $option['label'];
		} else {
			$label = '';
		}
		// attributes value, name, type, required
		$value = get_option($id, $default = $option['default'] );
		// $nameattr = $name; not necessary but good reminder
		$type = $option['type'];
		$required = ( isset($option['not_null']) && $option['not_null'] ) ? 'required="required"' : '' ;
		if ( isset( $option['values'] ) ){
			$valuesarray = $option['values'];
		}
		$html .= $before;
		if ($type == 'info'){
			if ( $label != '' ){
				$label = "<h2 id='{$id}'>$label</h2>";
			}
			$html .= $label;
			$html .= "<p>$value</p>";
			$html .= "<p class='description'>$description</p>";
			$html .= $after;
			echo $html;
			return;
		}
		$html .= "<label for='{$id}'>$label</label>";
		$html .= PHP_EOL;
		$value = stripslashes( $value );
		switch($type){
			case 'url':
				$html .= "<input id='{$id}' class='{$class}' {$required} type='url' name='{$name}' value='{$value}' />";
				break;
			case 'email':
				$html .= "<input id='{$id}' class='{$class}' {$required} type='email' name='{$name}' value='{$value}' />";
				break;
			case 'str':
				$html .= "<input id='{$id}' class='{$class}' {$required} type='text' name='{$name}' value='{$value}' />";
				break;
			case 'textarea':
				$html .= "<textarea id='{$id}' class='{$class}' {$required} type='text' name='{$name}' >{$value}</textarea>";
				break;
			case 'int' :
				$html .= "<input id='{$id}' class='{$class}' {$required} type='number' min='' max='' name='{$name}' value='{$value}' />";
				break;
			case 'bool' :
				$checked = ($value) ? 'checked="checked"' : '' ;
				$html .= "<input id='{$id}' class='{$class}' {$required} {$checked} type='checkbox' name='{$name}' value='1' />";
				break;
			case 'array' :
					echo "<select name='{$name}' id='{$id}'>";
				foreach( $valuesarray as $optionname => $label ){
					$checked = ( $value == $optionname ) ? 'checked="checked"' : '' ;
					echo "<option value='{$optionname}' {$checked} >{$label}</option>";
				}
					echo "</select>";
				break;
			default :
				break;
		}
		$html .= PHP_EOL;
		if ($description != ''){
			$html .= "<p class='description'>{$description}</p>";
			$html .= PHP_EOL;
		}
		$html .= $after;
		echo $html;
	}
	private function get_js_script_values($autocomplete = false){
		$array = array();
			if ($autocomplete){
				$array['pages'] = array(); // Left off here 8/22. For some reason when supplying post below I don't get anything. Perhaps there is another setting that is a default that makes no posts come up in the search...
				$pages = array();
				$posts = array();
				if($acpages = true){ //new setting needed!
					$posts = get_pages();
				} 
				if($acposts = true){ //new setting needed!
					$pages = get_posts();
				}
				$all = array_merge($posts, $pages);
				foreach ($all as $page){
					$array['pages'][] = $page->post_title;					
				}
			}
		foreach ($this->options as $name => $values) {
			if ( isset($values['hide_from_js'] ) && $values['hide_from_js'] === true ){
				continue;
			}

				

			$id = $this->option_prefix . $name;
			$value = get_option($id, $values['default'] );
			$value = stripcslashes($value);
			$array[$name] = $value;
		}
		return $array;
	}

	public function enqueue_scripts_and_styles(){
		$option = $this->option_prefix . 'debug';
		if (get_option( $option, $default = false )){
			$version = uniqid();
		} else {
			$version = $this->ver;
		}

		$autocomplete = true;
		$use_autocomplete = $this->option_prefix . 'use_autocomplete';

		if( get_option( $use_autocomplete, true ) ){
			wp_enqueue_script('jquery-ui-autocomplete');
			$autocomplete = true;
		}

		wp_register_style( 'tb-ajax-search-css', plugin_dir_url(__FILE__) . 'css/awesome-ajax-search.css', array(), $version, $media = 'all' );
		wp_register_script( 'tb-ajax-search-js', plugin_dir_url(__FILE__) . 'js/awesome-ajax-search.js', array('jquery'), $version );
		wp_enqueue_style( 'tb-ajax-search-css' );
		wp_enqueue_script('tb-ajax-search-js');
		$options = $this->get_js_script_values($autocomplete);
		wp_localize_script( 
			'tb-ajax-search-js',
			'Awesome_AJAX_Search_Options',
			$options
		);
	}
	public function test_function(){
		print_r( $this->something );
	}
	public function ajax_search_activate(){
		// global $plugin_options;
		// $this->plugin_options = $plugin_options;
		// $array = $plugin_options;
		// if(is_array($array)){
		// if (isset($array)){
		// $array = $this->options['baseurl'];
		// $this->something = $this->options;
		// load_plugin_textdomain('ais', false, dirname(plugin_basename(__FILE__)) . "/translation/" );
		// require_once("inc/awesome-ajax-search-options.php");
			foreach( $this->options as $name => $values){
			// 	print_r($name);
			// 	print_r($values);
				$option = $this->option_prefix . $name;
				if ($values['type'] == "info"){

					delete_option( $option );
					continue;
				} else {	
					add_option( $option, $values['default'] );
				}
			}
			// exit;
		// }
	}
}
$Awesome_AJAX_Search = new Awesome_AJAX_Search();
/* EOF */