<?php

defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseDriver;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;

return new class () implements ServiceProviderInterface {
	public function register(Container $container): void
	{
		$container->set(InstallerScriptInterface::class, new class ($container->get(AdministratorApplication::class)) implements InstallerScriptInterface {

			protected AdministratorApplication $app;

			protected DatabaseDriver $db;

			protected string $minimumJoomla = '4.2.0';

			protected string $minimumPhp = '8.1';

			protected array $updateMethods = [];

			public function __construct(AdministratorApplication $app)
			{
				$this->app = $app;
				$this->db  = Factory::getContainer()->get('DatabaseDriver');
			}

			public function install(InstallerAdapter $adapter): bool
			{
				$this->enablePlugin($adapter);

				return true;
			}

			public function update(InstallerAdapter $adapter): bool
			{
				return true;
			}

			public function uninstall(InstallerAdapter $adapter): bool
			{
				return true;
			}

			public function preflight(string $type, InstallerAdapter $adapter): bool
			{
				if (!$this->checkCompatible())
				{
					return false;
				}

				return true;
			}

			public function postflight(string $type, InstallerAdapter $adapter): bool
			{
				$installer = $adapter->getParent();
				if ($type !== 'uninstall')
				{
					$this->parseLayouts($installer->getManifest()->layouts, $installer);

					if ($type === 'update')
					{
						foreach ($this->updateMethods as $method)
						{
							if (method_exists($this, $method))
							{
								$this->$method($adapter);
							}
						}
					}
				}
				else
				{
					$this->removeLayouts($installer->getManifest()->layouts);
				}

				return true;
			}

			protected function checkCompatible(): bool
			{
				$app = Factory::getApplication();

				if (!(new Version())->isCompatible($this->minimumJoomla))
				{
					$app->enqueueMessage(Text::sprintf('Required version of Joomla! %s', $this->minimumJoomla),
						'error');

					return false;
				}

				if (!(version_compare(PHP_VERSION, $this->minimumPhp) >= 0))
				{
					$app->enqueueMessage(Text::sprintf('Required PHP version %s', $this->minimumPhp),
						'error');

					return false;
				}

				return true;
			}

			protected function enablePlugin(InstallerAdapter $adapter)
			{
				$plugin          = new \stdClass();
				$plugin->type    = 'plugin';
				$plugin->element = $adapter->getElement();
				$plugin->folder  = (string) $adapter->getParent()->manifest->attributes()['group'];
				$plugin->enabled = 1;

				$this->db->updateObject('#__extensions', $plugin, ['type', 'element', 'folder']);
			}

			public function parseLayouts(SimpleXMLElement $element = null, Installer $installer = null): bool
			{
				if (!$element || !count($element->children()))
				{
					return false;
				}

				$folder      = ((string) $element->attributes()->destination) ? '/' . $element->attributes()->destination : null;
				$destination = Path::clean(JPATH_ROOT . '/layouts' . $folder);

				$folder = (string) $element->attributes()->folder;
				$source = ($folder && file_exists($installer->getPath('source') . '/' . $folder))
					? $installer->getPath('source') . '/' . $folder : $installer->getPath('source');

				$copyFiles = [];
				foreach ($element->children() as $file)
				{
					$path['src']  = Path::clean($source . '/' . $file);
					$path['dest'] = Path::clean($destination . '/' . $file);

					$path['type'] = $file->getName() === 'folder' ? 'folder' : 'file';
					if (basename($path['dest']) !== $path['dest'])
					{
						$newdir = dirname($path['dest']);
						if (!Folder::create($newdir))
						{
							Log::add(Text::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $newdir), Log::WARNING, 'jerror');

							return false;
						}
					}

					$copyFiles[] = $path;
				}

				return $installer->copyFiles($copyFiles, true);
			}

			protected function removeLayouts(SimpleXMLElement $element = null): bool
			{
				if (!$element || !count($element->children()))
				{
					return false;
				}

				$files = $element->children();

				$folder = ((string) $element->attributes()->destination) ? '/' . $element->attributes()->destination : null;
				$source = Path::clean(JPATH_ROOT . '/layouts' . $folder);

				foreach ($files as $file)
				{
					$path = Path::clean($source . '/' . $file);

					if (is_dir($path))
					{
						$val = Folder::delete($path);
					}
					else
					{
						$val = File::delete($path);
					}

					if ($val === false)
					{
						Log::add('Failed to delete ' . $path, Log::WARNING, 'jerror');

						return false;
					}
				}

				if (!empty($folder))
				{
					Folder::delete($source);
				}

				return true;
			}
		});
	}
};