<?php namespace Zenit\Core\Env\Component;

use Zenit\Core\ServiceManager\Component\Service;
use Zenit\Core\ServiceManager\Interfaces\SharedService;

abstract class ConfigReader implements SharedService{

	use Service;

	public function __construct(){
		$vars = get_object_vars($this);
		foreach ($vars as $key => $env) if ($env) $this->$key = env($env);
	}
}