<?php

namespace RpcGenerator\Generation;

use RpcGenerator\Common\ClassFacts\ClassDefinition;

interface GeneratorStrategyInterface {
	public function generate(ClassDefinition $classDefinition): string;
}
