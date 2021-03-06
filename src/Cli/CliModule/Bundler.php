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

class Bundler extends CliModule{
	
	/** @var Config */
	protected $config;
	
	protected function configure(){
		$this
			->setName('bundler')
			->setDescription('bundler')
		;
	}

	
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$style = new SymfonyStyle($input, $output);

		$files = glob(env('root').'/*/*.php');
		print_r($files);

		//$this->seek(env('root'));
		echo "done\n";
	}
	
	protected function seek($path, $pattern = '*'){
		echo $path."\n";
		$files = glob($path.'/'.$pattern);
		foreach ($files as $file){
			if(is_file($file)){
				echo $file."\n";
			}
		}
		$dirs = glob($path.'/*', GLOB_ONLYDIR);
		foreach ($dirs as $dir){
			$this->seek($dir, $pattern);
		}
	}

}
