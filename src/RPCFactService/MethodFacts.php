<?php

namespace RpcGenerator\RPCFactService;

class MethodFacts {
	/**
	 * @param string $module
	 * @param string $name
	 * @param string $function
	 * @param class-string $className
	 * @param string $classMethodName
	 * @param string|null $requiredClassRight
	 * @param string|null $requiredMethodRight
	 */
	public function __construct(
		public string $module,
		public string $name,
		public string $function,
		public string $className,
		public string $classMethodName,
		public ?string $requiredClassRight,
		public ?string $requiredClassMethodRight
	) {}
}
