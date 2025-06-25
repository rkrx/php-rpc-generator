<?php

namespace RpcGenerator\Common;

use RpcGenerator\Common\ClassFacts\ClassDefinition;

interface IndexCacheInterface {
	public function isModified(string $fqClassName): bool;

	public function update(string $fqClassName, ClassDefinition $classDefinition): void;

	public function getClassDefinition(string $fqClassName): ClassDefinition;
}
