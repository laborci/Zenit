<?php namespace Zenit\Core\ServiceManager\Component;

use Zenit\Core\ServiceManager\Interfaces\SharedService;
class ServiceContainer{

	/** @var ServiceFactory[] */
	protected $services = [];

	protected static $instance;
	protected static function instance(){ return is_null(static::$instance) ? static::$instance = new static() : static::$instance; }
	protected function __construct(){ }

	public static function bind($name): ServiceFactory{ return static::instance()->bindService($name); }
	public static function shared($name): ServiceFactory{ return static::instance()->bindService($name)->shared(); }
	public static function get($name, $optional = false){ return static::instance()->getService($name, $optional); }
	public static function exists($name){return array_key_exists($name, static::instance()->services); }

	protected function bindService($serviceName){
		$service = new ServiceFactory($serviceName);
		$this->services[$serviceName] = $service;
		return $service;
	}

	protected function getService($name, $optional = false){
		$serviceFactory = null;
		if (array_key_exists($name, $this->services)){
			$serviceFactory = $this->services[$name];
		}else{
			try{
				$reflection = new \ReflectionClass($name);
				if (!$reflection->isInstantiable()) throw new \Exception('Not instantiable ' . $name);
				$serviceFactory = $this->bindService($name)->service($name);
				if ($reflection->implementsInterface(SharedService::class)) $serviceFactory->shared();
			}catch (\Exception $e){
				if ($optional) return null;
				else trigger_error('Service or Class as a service "' . $name . '" not found. Exception:' . $e->getMessage());
			}
		}
		return $serviceFactory ? $serviceFactory->get() : null;
	}

	public static function dump(){ return static::instance()->services; }
}

