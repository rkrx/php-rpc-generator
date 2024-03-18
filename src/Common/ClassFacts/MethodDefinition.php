<?php

namespace RpcGenerator\Common\ClassFacts;

class MethodDefinition {
	/**
	 * @param string $name
	 * @param string $methodName
	 * @param array<ParameterDefinition> $parameters
	 * @param TypeDefinition|null $return
	 */
	public function __construct(
		public string $name,
		public string $methodName,
		public array $parameters,
		public TypeDefinition|null $return,
	) {}
}
