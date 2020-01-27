<?php namespace Zenit\Core\Cli\CliModule;

use Application\Ghost\User;
use CaseHelper\CaseHelperFactory;
use Composer\Autoload\ClassLoader;
use Minime\Annotations\Reader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Zenit\Bundle\Mission\Component\Cli\CliModule;
use Zenit\Core\CodeWriter\Component\CodeWriter;
use Zenit\Core\Config;
use Zenit\Core\ServiceManager\Component\ServiceContainer;

class ApiExtractor extends CliModule{
	
	/** @var Config */
	protected $config;
	
	protected function configure(){
		$this
			->setName('apiextractor')
			->setAliases(['ae'])
			->setDescription('extract web api-s to js')
		;
	}

	
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$style = new SymfonyStyle($input, $output);
		$apis = $this->config;
		foreach ($apis as $api) if(!empty($api['extract'])) $this->extract($api);
		$style->success('done');
	}

	protected function extract($api){
		/** @var Reader $reader */
		$reader = ServiceContainer::get(Reader::class);

		$namespace = $api['namespace'];
		$path = $api['path'];

		$cw = new CodeWriter();
		$classes = $cw->Psr4ClassSeeker($namespace);

		$endpoints = [];

		foreach ($classes as $class){

			$url = explode('\\', trim($path, '/') . substr($class, strlen($namespace)));
			$url = array_map(function ($url){ return CaseHelperFactory::make(CaseHelperFactory::INPUT_TYPE_PASCAL_CASE)->toKebabCase($url); }, $url);

			$reflection = new \ReflectionClass($class);
			/** @var \ReflectionMethod $classMethod */
			$methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

			foreach ($methods as $method){
				$on = $reader->getAnnotations($method)->get('on');
				$accepts = $reader->getAnnotations($method)->get('accepts');
				$name = $reader->getAnnotations($method)->get('endpoint');
				if ( ($accepts || $on) && $name){
					$endpoint = [
						'name'     => $name,
						'url'      => join('/', $url) . ($on ? '' : '/' . CaseHelperFactory::make(CaseHelperFactory::INPUT_TYPE_PASCAL_CASE)->toKebabCase($method->getName())),
						'params'   => array_column(array_map(function (\ReflectionParameter $parameter){ return [$parameter->getName(), $parameter->isOptional()]; }, $method->getParameters()), 1, 0),
						'required' => $method->getNumberOfRequiredParameters(),
					];
					$endpoints[$endpoint['name']] = $endpoint;
				}
			}
		}

		$endpointMethods = [];
		foreach ($endpoints as $endpoint){
			$endpointMethod = '';

			$endpointMethod = "\t/**\n";
			foreach ($endpoint['params'] as $param => $optional) $endpointMethod .= "\t * @param " . ($optional ? "[" . $param . "]" : $param) . "\n";
			$endpointMethod .= "\t * @return {string}\n";
			$endpointMethod .= "\t */\n";

			$endpointMethod .= "\t" . $endpoint['name'] . ": function(" . join(", ", array_keys($endpoint['params'])) . "){\n";
			if ($endpoint['required']) $endpointMethod .= "\t\tif(arguments.length < " . $endpoint['required'] . ") throw new Error();\n";
			$endpointMethod .= "\t\treturn \"/" . $endpoint['url'] . (!empty($endpoint['params']) ? "/\" + [...arguments].join('/')" : "\"") . ";\n";
			$endpointMethod .= "\t}";
			$endpointMethods[] = $endpointMethod;
		}



		foreach ($api['extract'] as $extract){
			$file = $extract['file'];
			$as = $extract['as'];
			file_put_contents( $file, "let " . $as . " = {\n" . join(",\n", $endpointMethods) . "\n};\nexport default " . $as . ";");
		}
	}

}