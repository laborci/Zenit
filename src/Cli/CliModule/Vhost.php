<?php namespace Zenit\Core\Cli\CliModule;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zenit\Bundle\Mission\Component\Cli\CliModule;
use Zenit\Core\Config;

class Vhost extends CliModule{

	protected function configure() {
		$this
			->setName('generate-vhost')
			->setAliases(['vhost'])
			->setDescription('Generates vhost file from the template');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$style = new SymfonyStyle($input, $output);

		$files = Config::Service()->vhost;

		foreach ($files as $name=>$file){
			$source = $file['template'];
			$target = $file['output'];

			$template = file_get_contents($source);
			preg_match_all('/\{\{(.*?)\}\}/', $template, $matches);
			$keys = array_unique($matches[1]);
			foreach ($keys as $key) $template = str_replace('{{' . $key . '}}', env($key), $template);
			file_put_contents($target, $template);
			$style->success($name.' Done');

		}

	}

}
