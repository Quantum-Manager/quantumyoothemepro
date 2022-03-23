<?php
/**
 * @package    quantumyoothemepro
 *
 * @author     tsymb <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * Quantumyoothemepro script file.
 *
 * @package   quantumyoothemepro
 * @since     1.0.0
 */
class plgSystemQuantumyoothemeproInstallerScript
{


	/**
	 * Called after any type of action
	 *
	 * @param   string            $route    Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter)
	{
		if ($route === 'install')
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('extension_id');
			$query->from('#__extensions');
			$query->where($db->qn('element') . ' = ' . $db->q('yootheme'));
			$query->where($db->qn('type') . ' = ' . $db->q('plugin'));
			$query->where($db->qn('folder') . ' = ' . $db->q('system'));
			$result = $db->setQuery($query)->loadObject();

			if (!empty($result->extension_id))
			{
				$this->enablePlugin($adapter);
			}
		}

	}

	protected function enablePlugin($parent)
	{
		$plugin          = new stdClass();
		$plugin->type    = 'plugin';
		$plugin->element = $parent->getElement();
		$plugin->folder  = (string) $parent->getParent()->manifest->attributes()['group'];
		$plugin->enabled = 1;

		Factory::getDbo()->updateObject('#__extensions', $plugin, ['type', 'element', 'folder']);
	}

}
