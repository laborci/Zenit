<?php namespace Zenit\Core\Cli\CliModule;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zenit\Bundle\Mission\Component\Cli\CliModule;
use Zenit\Core\Env\Component\EnvLoader;

class ShowEnv extends CliModule{

	protected function configure(){
		$this->setName('showenv');
		$this->setAliases(['se']);
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$arr = EnvLoader::load();
		$env = [];
		foreach ($arr as $key=>$value){
			if(!is_array($value)) $env[] = [$key, $value];
		}
		$table = new Table($output);
		$table
			->setHeaders(['key', 'value'])
			->setRows($env)
		;
		$table->render();
	}

}
