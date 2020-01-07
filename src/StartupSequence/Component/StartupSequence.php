<?php namespace Zenit\Core\StartupSequence\Component;

use Minime\Annotations\Cache\FileCache;
use Minime\Annotations\Parser;
use Minime\Annotations\Reader;
use Zenit\Core\Env\Component\Env;
use Zenit\Core\Env\Component\EnvLoader;
use Zenit\Core\Module\Component\ModuleLoader;
use Zenit\Core\ServiceManager\Component\ServiceContainer;
use Symfony\Component\HttpFoundation\Request;

class StartupSequence{

	public function __construct($root, $ini_path = "etc/ini/", $ini_file = 'env', $env_path = "var/", $env_build_file = 'env.php'){

		putenv('root=' . realpath($root) . '/');
		putenv('env-path=' . getenv('root') . $env_path);
		putenv('env-build-file=' . $env_build_file);
		putenv('ini-path=' . getenv('root') . $ini_path);
		putenv('ini-file=' . $ini_file);
		putenv('context=' . (http_response_code() ? 'WEB' : 'CLI'));

		if (!EnvLoader::checkCache()) EnvLoader::save();
		Env::Service()->store(include getenv('env-path') . getenv('env-build-file'));

		setenv('root', getenv('root'));
		setenv('context', getenv('context'));

		if (env('output-buffering')) ob_start();
		date_default_timezone_set(env('timezone'));
		if (getenv('context') === 'WEB') session_start();

		$config = Config::Service();

		ServiceContainer::shared(Request::class)->factoryStatic([Request::class, 'createFromGlobals']);
		ServiceContainer::shared(Reader::class)->factory(function () use ($config){ return new Reader(new Parser(), new FileCache($config->annotationReaderCache)); });

		$moduleLoader = ModuleLoader::Service();
		$moduleLoader->registerModuleAliases($config->moduleAliases);

		foreach ($config->modules as $module => $config) $moduleLoader->loadModule($module, $config);
	}

}

