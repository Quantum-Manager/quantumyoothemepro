<?php
/**
 * @package    quantummanager
 *
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseDriver;

defined('_JEXEC') or die;

/**
 * Quantumyoothemepro plugin.
 *
 * @package   quantumyoothemepro
 * @since     1.0.0
 */
class plgSystemQuantumyoothemepro extends CMSPlugin
{
	/**
	 * Application object
	 *
	 * @var    CMSApplication
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    DatabaseDriver
	 * @since  1.0.0
	 */
	protected $db;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;


	/**
	 * onAfterRender.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function onBeforeCompileHead()
	{

		$admin = $this->app->isClient('administrator');
		$p = $this->app->input->getCmd('p', '');

		if(!$admin)
		{
			return;
		}

		if(empty($p))
		{
			return;
		}

		HTMLHelper::_('jquery.framework');

		HTMLHelper::_('stylesheet', 'plg_system_quantumyoothemepro/yoothemepro.css', [
			'version' => filemtime(__FILE__),
			'relative' => true
		]);

		HTMLHelper::_('script', 'plg_system_quantumyoothemepro/yoothemepro.js', [
			'version' => filemtime(__FILE__),
			'relative' => true
		]);

	}


	public function onAjaxQuantumyoothemepro()
	{
		$layout = new FileLayout('modal', JPATH_SITE . '/plugins/system/quantumyoothemepro/tmpl');
		echo $layout->render();
		//$this->app->close();
	}

}
