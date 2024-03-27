<?php

namespace RpcGenerator\Common\IndexCache;

use RpcGenerator\Common\ClassFacts\ClassDefinition;
use RpcGenerator\Common\IndexCacheInterface;
use RuntimeException;

class NullCache implements IndexCacheInterface {
	public function isModified(string $path): bool {
		return true;
	}

	public function update(string $path, ClassDefinition $classDefinition): void {
	}

	public function getClassDefinition(string $file): ClassDefinition {
		throw new RuntimeException('Not implemented');
	}
}
