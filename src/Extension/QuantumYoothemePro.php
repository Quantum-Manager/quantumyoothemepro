<?php

namespace Joomla\Plugin\System\QuantumYoothemePro\Extension;

/**
 * @package    quantummanager
 *
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;
use Joomla\Event\SubscriberInterface;

class QuantumYoothemePro extends CMSPlugin implements SubscriberInterface
{

	protected $app;

	protected $db;

	protected $autoloadLanguage = true;

	public static function getSubscribedEvents(): array
	{
		return [
			'onBeforeCompileHead'      => 'onBeforeCompileHead',
			'onAjaxQuantumyoothemepro' => 'onAjax',
		];
	}

	public function onBeforeCompileHead(): void
	{
		$admin = $this->app->isClient('administrator');
		$p     = $this->app->input->getCmd('p', '');

		if (!$admin || empty($p))
		{
			return;
		}

		HTMLHelper::_('script', 'plg_system_quantumyoothemepro/modal.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

		HTMLHelper::_('script', 'com_quantummanager/utils.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

		QuantummanagerHelper::loadLang();

		$insert = htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SELECT'), ENT_QUOTES);
		$cancel = htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_CANCEL'), ENT_QUOTES);

		/** @var WebAssetManager $wa */
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
		$wa->addInlineScript(<<<EOT
window.QuantumYoothemeproLang = {
		'insert': "{$insert}",
		'cancel': "{$cancel}",
};
EOT
		);
	}

	public function onAjax(): void
	{
		$layout = new FileLayout('select', JPATH_SITE . '/plugins/system/quantumyoothemepro/tmpl');
		echo $layout->render();
	}

}
