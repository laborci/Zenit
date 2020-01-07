<?php namespace Zenit\Core\Module\Interfaces;

interface ModuleInterface extends \Zenit\Core\ServiceManager\Interfaces\SharedService{
	public function load($config);
}