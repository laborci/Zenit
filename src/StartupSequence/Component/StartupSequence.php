<?php namespace Zenit\Core\StartupSequence\Component;

use Composer\Autoload\ClassLoader;
use Minime\Annotations\Cache\FileCache;
use Minime\Annotations\Parser;
use Minime\Annotations\Reader;
use Zenit\Core\Config;
use Zenit\Core\Env\Component\Env;
use Zenit\Core\Env\Component\EnvLoader;
use Zenit\Core\Event\Component\EventManager;
use Zenit\Core\Module\Component\ModuleLoader;
use Zenit\Core\ServiceManager\Component\ServiceContainer;
use Symfony\Component\HttpFoundation\Request;
use Zenit\Core\StartupSequence\Constant\StartupEvent;

class StartupSequence{

	public function __construct($root, $ini = "etc/ini/env", $env = "var/env.php", ClassLoader $classLoader){

		Env::loadFacades();
		putenv('root=' . realpath($root) . '/');
		putenv('context=' . (http_response_code() ? 'WEB' : 'CLI'));
		putenv('env-file=' . getenv('root') . $env);
		putenv('ini-path=' . getenv('root') . dirname($ini) . '/');
		putenv('ini-file=' . basename($ini));

		if (!EnvLoader::checkCache()) EnvLoader::save();
		EnvLoader::save();
		Env::Service()->store(include getenv('env-file'));
		Env::Service()->set('root', getenv('root'));
		Env::Service()->set('context', getenv('context'));

		$config = Config::Service();

		date_default_timezone_set($config->timezone);
		if ($config->outputBuffering) ob_start();
		if ($config->context === 'WEB') session_start();

		ServiceContainer::shared(Request::class)->factoryStatic([Request::class, 'createFromGlobals']);
		ServiceContainer::shared(Reader::class)->factory(function () use ($config){ return new Reader(new Parser(), new FileCache($config->annotationReaderCache)); });
		ServiceContainer::shared(ClassLoader::class)->factory(function () use ($classLoader){ return $classLoader; });

		$moduleLoader = ModuleLoader::Service();
		$moduleLoader->registerModuleAliases($config->moduleAliases);
		$moduleLoader->loadModules($config->modules);

		EventManager::fire(StartupEvent::MODULES_LOADED);
		EventManager::fire(StartupEvent::DONE);
	}

}

