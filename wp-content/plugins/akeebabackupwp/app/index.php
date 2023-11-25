<?php
/**
 * @package   solo
 * @copyright Copyright (c)2014-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Awf\Application\Application;
use Solo\Container;

defined('AKEEBASOLO') || define('AKEEBASOLO', 1);

/**
 * @var Container $akeebaSoloContainer
 */
require __DIR__ . '/include.php';

try
{
	$application = $akeebaSoloContainer->application;

	$application->initialise();
	$application->route();
	$application->dispatch();
	$application->render();
	$application->close();
}
catch (Throwable $exc)
{
	$filename = null;

	if ($application instanceof Application)
	{
		$template = $application->getTemplate();
		$filename = APATH_THEMES . '/' . $template . '/error.php';
		$filename = @file_exists($filename) ? $filename : null;
	}

	if (is_null($filename))
	{
		echo "<h1>Application Error</h1>\n";
		echo "<p>Please submit the following error message and trace in its entirety when requesting support</p>\n";
		echo "<div class=\"alert alert-danger\">" . get_class($exc) . ' &mdash; ' . $exc->getMessage() . "</div>\n";
		echo "<pre class=\"well\">\n";
		echo $exc->getTraceAsString();
		echo "</pre>\n";

		return;
	}

	include $filename;
}