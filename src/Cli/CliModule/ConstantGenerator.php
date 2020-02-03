<?php namespace Zenit\Core\Cli\CliModule;

use Application\Ghost\User;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Zenit\Bundle\Mission\Component\Cli\CliModule;
use Zenit\Core\Code\Component\CodeFinder;
use Zenit\Core\Config;
use Zenit\Core\Constant\Component\Constant;
use Zenit\Core\ServiceManager\Component\ServiceContainer;

class ConstantGenerator extends CliModule{
	
	/** @var Config */
	protected $config;
	
	protected function configure(){
		$this
			->setName('constantgenerator')
			->setAliases(['cg'])
			->setDescription('compose constants')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$style = new SymfonyStyle($input, $output);

		$classes = array_key_exists('classes', $this->config) && is_array($this->config['classes']) ? $this->config['classes'] : [];

		$cf = CodeFinder::Service();

		if(array_key_exists('namespaces', $this->config) && is_array($this->config['namespaces'])){
			foreach ($this->config['namespaces'] as $namespace) $classes = array_merge($classes, $cf->Psr4ClassSeeker($namespace) );
		}
		
		foreach ($classes as $class){
			if(is_subclass_of($class, Constant::class))$class::generate();
		}
		$style->success('done');
	}


}
