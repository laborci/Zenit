<?php namespace Zenit\Core\Env\Component;

use Zenit\Core\ServiceManager\Component\Service;
use Zenit\Core\ServiceManager\Interfaces\SharedService;

class Env implements SharedService {

	use Service;

	protected $env = [];

	public function store($env) {$this->env = $env;}
	public function get($key = null) {
		if($key === null) return $this->env;
		if(array_key_exists($key, $this->env)) return $this->env[$key];
		return DotArray::get($this->env, $key, null);
	}
	public function set($key, $value) { DotArray::set($this->env, $key, $value); }
	static public function loadFacades() { include __DIR__."/../Facade/env.php"; }

}