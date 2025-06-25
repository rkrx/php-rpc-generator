<?php

namespace RpcGenerator\Common\IndexCache;

use Exception;
use RpcGenerator\Common\ClassFacts\ClassDefinition;
use RpcGenerator\Common\IndexCacheInterface;
use Throwable;

class FileCache implements IndexCacheInterface {
	/**
	 * @var null|array<string, array{mtime: int, def: ClassDefinition}>
	 */
	private ?array $cache = null;

	/**
	 * @param string $cacheFilePath The file path where the cache data is stored in.
	 * @param string $seed The seed is used to invalidate the cache when some managed state outside of the cache changes.
	 */
	public function __construct(
		private string $cacheFilePath,
		private string $seed = ''
	) {}

	public function isModified(string $fqClassName): bool {
		if($this->cache === null) {
			$this->cache = $this->loadCache();
		}

		if(!array_key_exists($fqClassName, $this->cache)) {
			return false;
		}

		return $this->cache[$fqClassName]['mtime'] !== filemtime($fqClassName);
	}

	public function getClassDefinition(string $fqClassName): ClassDefinition {
		$def = $this->cache[$fqClassName]['def'] ?? null;
		if($def === null) {
			throw new Exception('Invalid class definition');
		}
		$classDefinition = unserialize($def);
		if($classDefinition instanceof ClassDefinition) {
			return $classDefinition;
		}
		throw new Exception('Invalid class definition');
	}

	public function update(string $fqClassName, ClassDefinition $classDefinition): void {
		if($this->cache === null) {
			$this->cache = $this->loadCache();
		}

		$this->cache[$fqClassName] = ['mtime' => filemtime($fqClassName), 'def' => serialize($classDefinition)];
		file_put_contents($this->cacheFilePath, sprintf("<?php return %s;\n", var_export(['seed' => $this->seed, 'cache' => $this->cache], true)));
	}

	/**
	 * @return array<string, array{mtime: int}>
	 */
	private function loadCache(): array {
		try {
			if(!file_exists($this->cacheFilePath)) {
				return [];
			}
			$data = require $this->cacheFilePath;
			if(!is_array($data)) {
				return [];
			}
			if(($data['seed'] ?? null) !== $this->seed) {
				return [];
			}
			return $data['cache'] ?? [];
		} catch(Throwable $e) {
			return [];
		}
	}
}
