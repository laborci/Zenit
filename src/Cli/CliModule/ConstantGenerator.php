<?php namespace Zenit\Core\Cli\CliModule;

use Application\Ghost\User;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Zenit\Bundle\Mission\Component\Cli\CliModule;
use Zenit\Core\Config;
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
		$classes = $this->config;
		foreach ($classes as $class)$class::generate();
		$style->success('done');
	}


}
