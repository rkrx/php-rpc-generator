<?php

namespace RpcGenerator\Common\ClassFacts;

class TypeDefinition {
	/**
	 * @param bool $nullable
	 * @param array<AtomarTypeDefinition> $types
	 */
	public function __construct(
		public bool $nullable,
		public array $types,
	) {}
}