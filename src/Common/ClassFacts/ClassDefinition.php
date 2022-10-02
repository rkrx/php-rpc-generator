<?php

namespace RpcGenerator\Common\ClassFacts;

class ClassDefinition {
	/**
	 * @param string|null $module
	 * @param string $name
	 * @param string $fqClassName
	 * @param string $className
	 * @param array<MethodDefinition> $methods
	 */
	public function __construct(
		public ?string $module,
		public string $name,
		public string $fqClassName,
		public string $className,
		public array $methods,
	) {}
}
