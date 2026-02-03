<?php

defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseDriver;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
	public function register(Container $container): void
	{
		$container->set(InstallerScriptInterface::class, new class ($container->get(AdministratorApplication::class)) implements InstallerScriptInterface {

			protected AdministratorApplication $app;

			protected DatabaseDriver $db;

			protected string $minimumJoomla = '4.2.0';

			protected string $minimumPhp = '8.1';

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
		});
	}
};