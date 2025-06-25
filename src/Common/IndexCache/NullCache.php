<?php

namespace RpcGenerator\Common\IndexCache;

use RpcGenerator\Common\ClassFacts\ClassDefinition;
use RpcGenerator\Common\IndexCacheInterface;
use RuntimeException;

class NullCache implements IndexCacheInterface {
	public function isModified(string $fqClassName): bool {
		return true;
	}

	public function update(string $fqClassName, ClassDefinition $classDefinition): void {
	}

	public function getClassDefinition(string $fqClassName): ClassDefinition {
		throw new RuntimeException('Not implemented');
	}
}
