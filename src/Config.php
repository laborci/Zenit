<?php namespace Zenit\Core;

use Zenit\Core\Env\Component\ConfigReader;

class Config extends ConfigReader{

	public $annotationReaderCache = 'path.annotation-reader-cache';

	/** @var array */
	public $moduleAliases = 'sys.module-aliases';
	/** @var array */
	public $modules = 'sys.modules';
	/** @var boolean  */
	public $outputBuffering = 'sys.output-buffering';
	public $timezone = 'sys.timezone';
	public $context = 'sys.context';
	public $vhost = 'sys.vhost';
	public $root = 'root';
	public $dictSource = 'sys.dict.source';

}


