<?php

namespace RpcGenerator;

use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RpcGenerator\Attributes\RPCClass;
use RpcGenerator\Attributes\RPCMethod;
use RpcGenerator\Common\ClassFactGatheringService;
use RpcGenerator\Common\ClassGenerationResult;
use RpcGenerator\Common\Configuration;
use RuntimeException;

class RPCGenerator {
	public function __construct(private Configuration $configuration) {}

	/**
	 * @param string $className
	 * @return array{string, string}
	 */
	private static function getNSAndShortName(string $className): array {
		if(preg_match('{^(.*?)\\\\([^\\\\]+)$}', $className, $matches)) {
			return [$matches[1], $matches[2]];
		}
		throw new RuntimeException('Invalid class name (e.g. class name has no namespace)');
	}

	/**
	 * @param string|null $filePattern
	 * @param string ...$directories
	 * @return Generator<ClassGenerationResult>
	 */
	public function findClassesAndBuildIndex(?string $filePattern = null, string ...$directories) {
		$index = $this->configuration->indexCache->fetch();

		$result = [];

		$filePattern ??= $this->configuration->filePattern;
		$classes = $this->findClasses($filePattern, ...$directories);
		foreach($classes as $file => $className) {
			if(array_key_exists($file, $index)) {
				$cachedMTime = $index[$file]['mtime'];
				filemtime($file);
			}

			$classDefinition = ClassFactGatheringService::getFacts($className);
			$generatorStrategy = $this->configuration->generatorStrategy;

			yield new ClassGenerationResult(
				def: $classDefinition,
				body: $generatorStrategy->generate($classDefinition)
			);
		}
	}

	/**
	 * @param string ...$directories
	 * @return Generator<string>
	 */
	private function findClasses(string $filePattern, string ...$directories): Generator {
		foreach($directories as $directory) {
			$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

			foreach($rii as $file) {
				if($file->isDir()){
					continue;
				}

				$filepath = $file->getPathname();
				if(!preg_match($filePattern, $filepath)) {
					continue;
				}

				foreach($this->configuration->classFinder->findAllClassesInFile($filepath) as $className) {
					$contents = (string) file_get_contents($filepath);
					$test = static fn(string $str) => str_contains($contents, $str);

					[$rpcClassNS, $rpcClassShortName] = self::getNSAndShortName(RPCClass::class);
					[$rpcMethodNS, $rpcMethodShortName] = self::getNSAndShortName(RPCMethod::class);

					if(($test($rpcClassNS) && $test($rpcClassShortName)) || ($test($rpcMethodNS) && $test($rpcMethodShortName))) {
						yield $filepath => $className;
					}
				}
			}
		}
	}
}
