<?php namespace Zenit\Core\StartupSequence\Component;

use Zenit\Core\Env\Component\ConfigReader;

class Config extends ConfigReader{
	/** @var array */
	public $moduleAliases = 'module-aliases';
	/** @var array */
	public $modules = 'modules';
	public $annotationReaderCache = 'annotation-reader.cache';
}


