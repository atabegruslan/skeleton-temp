<?php
/**
 * @package   solo
 * @copyright Copyright (c)2014-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Solo;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Awf\Text\Text;
use Solo\Helper\Html\FEFSelect as FEFSelectHtmlHelper;
use Solo\Helper\Html\Setup as SetupHtmlHelper;
use Solo\Helper\SecretWord;

class Application extends \Awf\Application\Application
{
	private static $loadedLanguages = false;

	public function initialise()
	{
		// Register additional HTML Helpers
		$this->getContainer()->html->registerHelperClass(SetupHtmlHelper::class);
		$this->getContainer()->html->registerHelperClass(FEFSelectHtmlHelper::class);

		// Let AWF know that the prefix for our system JavaScript is 'akeeba.System.'
		$this->getContainer()->html->grid->setJavascriptPrefix('akeeba.System.');

		// Put a small marker to indicate that we run inside another CMS
		$isCMS = $this->setIsCMSFlag();

		// Get the target platform information for updates
		$this->setupUpdatePlatform();

		// Set up the template (theme) to use
		if ($isCMS)
		{
			$this->setTemplate('wp');
		}

		// Load language files
		$this->loadLanguages();

		// Load the configuration file if it's present
		$this->container->appConfig->loadConfiguration();

		// Enforce encryption of the front-end Secret Word
		SecretWord::enforceEncryption('frontend_secret_word', $this->container);

		// Load Akeeba Engine's configuration
		$this->loadBackupProfile();

		// Attach the user privileges to the user manager
		$manager = $this->container->userManager;

		$this->attachPrivileges($manager);

		// Set up the media query key
		$this->setupMediaVersioning();
	}

	/**
	 * Language file processing callback. It converts _QQ_ to " and replaces the product name in the legacy INI files
	 * imported from Akeeba Backup for Joomla!.
	 *
	 * @param   string  $filename  The full path to the file being loaded
	 * @param   array   $strings   The key/value array of the translations
	 *
	 * @return  boolean|array  False to prevent loading the file, or array of processed language string, or true to
	 *                         ignore this processing callback.
	 */
	public function processLanguageIniFile($filename, $strings)
	{
		foreach ($strings as $k => $v)
		{
			$v           = str_replace('_QQ_', '"', $v);
			$v           = str_replace('Akeeba Solo', 'Akeeba Backup', $v);
			$v           = str_replace('Akeeba Backup', 'Akeeba Backup for WordPress', $v);
			$v           = str_replace('Joomla!', 'WordPress', $v);
			$v           = str_replace('Joomla', 'WordPress', $v);
			$strings[$k] = $v;
		}

		return $strings;
	}

	/**
	 * @return bool
	 */
	private function setIsCMSFlag()
	{
		$isCMS = defined('WPINC');
		$this->container->segment->set('insideCMS', $isCMS);

		return $isCMS;
	}

	/**
	 * @return void
	 */
	public function setupUpdatePlatform()
	{
		$platformVersion = function_exists('get_bloginfo') ? get_bloginfo('version') : '0.0';
		$this->container->segment->set('platformNameForUpdates', 'wordpress');
		$this->container->segment->set('platformVersionForUpdates', $platformVersion);
	}

	/**
	 * @return void
	 */
	private function loadLanguages()
	{
		if (self::$loadedLanguages)
		{
			return;
		}

		self::$loadedLanguages = true;

		// Load the language files
		Text::loadLanguage('en-GB', $this->container, '.ini', true, $this->container->languagePath . '/akeebabackup', [$this, 'processLanguageIniFile']);

		if (Text::detectLanguage($this->container, '.ini', $this->container->languagePath . '/akeebabackup', )
		    !== 'en-GB')
		{
			Text::loadLanguage(null, $this->container, '.ini', true, $this->container->languagePath . '/akeebabackup', [$this, 'processLanguageIniFile']);
		}
	}

	/**
	 * @return void
	 */
	private function loadBackupProfile()
	{
		try
		{
			Platform::getInstance()->load_configuration();
		}
		catch (\Exception $e)
		{
			// Ignore database exceptions, they simply mean we need to install or update the database
		}
	}

	/**
	 * @param $manager
	 *
	 * @return void
	 */
	private function attachPrivileges($manager)
	{
		$manager->registerPrivilegePlugin('akeeba', '\\Solo\\Application\\WordpressUserPrivileges');
	}

	/**
	 * @return void
	 */
	private function setupMediaVersioning()
	{
		$this->getContainer()->mediaQueryKey = md5(microtime(false));
		$isDebug                             = !defined('AKEEBADEBUG');
		$hasVersion                          = defined('AKEEBABACKUP_VERSION') && defined('AKEEBABACKUP_DATE');
		$isDevelopment                       = $hasVersion ? ((strpos(AKEEBABACKUP_VERSION, 'svn') !== false) || (strpos(AKEEBABACKUP_VERSION, 'dev') !== false) || (strpos(AKEEBABACKUP_VERSION, 'rev') !== false)) : true;

		if (!$isDebug && !$isDevelopment && $hasVersion)
		{
			$this->getContainer()->mediaQueryKey = md5(AKEEBABACKUP_VERSION . AKEEBABACKUP_DATE);
		}
	}
}
