<?php

namespace RpcGenerator\Generation\Common;

use RpcGenerator\Common\ClassFacts\ClassDefinition;
use RpcGenerator\Common\ClassFacts\MethodDefinition;

interface LinkInterface {
	public function generateLink(ClassDefinition $classDefinition, MethodDefinition $methodDefinition): string;
}
