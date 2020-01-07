<?php namespace Zenit\Core\Module\Component;

use Zenit\Core\Module\Interfaces\ModuleInterface;
use Zenit\Core\ServiceManager\Component\Service;
use Zenit\Core\ServiceManager\Component\ServiceContainer;
use Zenit\Core\ServiceManager\Interfaces\SharedService;

class ModuleLoader implements SharedService{
	use Service;

	protected $modules = [];
	protected $aliases = [];

	public function registerModuleAliases($aliases){
		if (is_array($aliases)) $this->aliases = $aliases;
	}

	public function loadModule(string $module, $config){
		$class = array_key_exists($module, $this->aliases) ? $this->aliases[$module] : $module;
		/** @var \Zenit\Core\Module\Interfaces\ModuleInterface $moduleInstance */
		$moduleInstance = ServiceContainer::get($class);
		$key = get_class($moduleInstance);
		if (array_key_exists($key, $this->modules)) throw new \Exception('Module already loaded: ' . $key);
		$this->modules[$key] = $moduleInstance;
		$this->modules[$module] = $moduleInstance;
		(function(ModuleInterface $module, $config){$module->load($config);})($moduleInstance, $config);
		return $moduleInstance;
	}

	public function get($module): ModuleInterface{ return $this->modules[$module]; }
}