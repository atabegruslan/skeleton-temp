<?php
/**
 * @package   solo
 * @copyright Copyright (c)2014-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/*
Plugin Name: Akeeba Backup CORE for WordPress
Plugin URI: https://www.akeeba.com
Description: The complete backup solution for WordPress
Version: 8.1.1
Requires at least: 6.3.0
Requires PHP: 7.4
Author: Akeeba Ltd
Author URI: https://www.akeeba.com
Network: true
License: GPL-3.0-or-later
*/

/**
 * Make sure we are being called from WordPress itself
 */
defined('WPINC') or die;

/**
 * This should never happen unless your site is broken! It'd mean that you're double loading our plugin which is not how
 * WordPress works. We still defend against this because we've learned to expect the unexpected ;)
 */
if (defined('AKEEBABACKUPWP_PATH'))
{
	return;
}

// Preload our helper classes
require_once dirname(__FILE__) . '/helpers/AkeebaBackupWP.php';
require_once dirname(__FILE__) . '/helpers/AkeebaBackupWPUpdater.php';

// Initialization of our helper class
AkeebaBackupWP::initialization(__FILE__);
AkeebaBackupWP::loadIntegratedUpdater(__FILE__);

// Quit early if it is the wrong PHP version
if (AkeebaBackupWP::$wrongPHP)
{
	return;
}

/**
 * Redirect to the ANGIE installer if the installer currently exists
 */
AkeebaBackupWP::redirectIfInstallationPresent();

/**
 * Register public plugin hooks
 */
register_activation_hook(__FILE__, ['AkeebaBackupWP', 'install']);

/**
 * Register public plugin deactivation hooks
 *
 * This is called when the plugin is deactivated which precedes (but does not necessarily imply) uninstallation.
 */
register_deactivation_hook(__FILE__, ['AkeebaBackupWP', 'onDeactivate']);

register_uninstall_hook(__FILE__, ['AkeebaBackupWP', 'uninstall']);

/**
 * Register administrator plugin hooks
 */
if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX))
{
	// Menu items
	add_action('admin_menu', ['AkeebaBackupWP', 'adminMenu']);
	add_action('network_admin_menu', ['AkeebaBackupWP', 'networkAdminMenu']);

	// Output buffering, wherever it is needed
	add_action('init', ['AkeebaBackupWP', 'startOutputBuffering'], 1);
	add_action('in_admin_footer', ['AkeebaBackupWP', 'stopOutputBuffering']);

	add_action('init', ['AkeebaBackupWP', 'loadCommonCSS'], 1);
	add_action('clear_auth_cookie', ['AkeebaBackupWP', 'onUserLogout'], 1);
}
elseif (defined('DOING_AJAX') && DOING_AJAX)
{
	add_action('wp_ajax_akeebabackup_api', ['AkeebaBackupWP', 'jsonApi'], 1);
	add_action('wp_ajax_nopriv_akeebabackup_api', ['AkeebaBackupWP', 'jsonApi'], 1);

	add_action('wp_ajax_akeebabackup_legacy', ['AkeebaBackupWP', 'legacyFrontendBackup'], 1);
	add_action('wp_ajax_nopriv_akeebabackup_legacy', ['AkeebaBackupWP', 'legacyFrontendBackup'], 1);

	add_action('wp_ajax_akeebabackup_check', ['AkeebaBackupWP', 'frontendBackupCheck'], 1);
	add_action('wp_ajax_nopriv_akeebabackup_check', ['AkeebaBackupWP', 'frontendBackupCheck'], 1);
}

// PseudoCRON with WP-CRON
// -- Add an "every ten seconds" interval rule (schedule)
add_filter('cron_schedules', function ($schedules) {
	$interval = max(defined('WP_CRON_LOCK_TIMEOUT') ? WP_CRON_LOCK_TIMEOUT : 60, 10);

	$schedules['akeebabackup_interval'] = [
		'interval' => $interval,
		'display'  => sprintf(__('Every %s seconds'), $interval),
	];

	return $schedules;
});

// -- Register the abwp_cron_scheduling action
add_action('abwp_cron_scheduling', ['AkeebaBackupWP', 'handlePseudoCron']);
// -- Make sure the abwp_cron_scheduling action is scheduled to run once every 10 seconds
if (!wp_next_scheduled('abwp_cron_scheduling'))
{
	wp_schedule_event(time(), 'akeebabackup_interval', 'abwp_cron_scheduling');
}

// Register WP-CLI commands
if (defined('WP_CLI') && WP_CLI)
{
	if (file_exists(__DIR__ . '/wpcli/register_commands.php'))
	{
		require_once __DIR__ . '/wpcli/register_commands.php';
	}
}
