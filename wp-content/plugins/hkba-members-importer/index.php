<?php

/*
Plugin Name: HKBA Members Importer
Plugin URI:  https://hkba.org.tw
Description: Imports the large numbers of old HKBA users
Version:     1.0
Author:      Ruslan Aliyev
Author URI:  https://xxx.com
License:     License
License URI: https://xxx.com
*/

function change_cpt_dashicon()
{
	// just an example
}
add_action('admin_head', 'change_cpt_dashicon');

function correct_english_plurals()
{
	// just an example
}
add_action('admin_menu', 'correct_english_plurals');

function title_to_upper($title)
{
	// just an example
}
add_filter('the_title', 'title_to_upper');
