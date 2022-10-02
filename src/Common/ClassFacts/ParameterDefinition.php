<?php

namespace RpcGenerator\Common\ClassFacts;

class ParameterDefinition {
	/**
	 * @param string $name
	 * @param bool $nullable
	 * @param TypeDefinition $typing
	 * @param DefaultValueDefinition $default
	 */
	public function __construct(
		public string $name,
		public bool $nullable,
		public TypeDefinition $typing,
		public DefaultValueDefinition $default,
	) {}
}