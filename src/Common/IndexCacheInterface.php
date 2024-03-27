<?php

namespace RpcGenerator\Common;

use RpcGenerator\Common\ClassFacts\ClassDefinition;

interface IndexCacheInterface {
	public function isModified(string $path): bool;

	public function update(string $path, ClassDefinition $classDefinition): void;

	public function getClassDefinition(string $file): ClassDefinition;
}
