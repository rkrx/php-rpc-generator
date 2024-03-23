<?php

namespace RpcGenerator\RPCFactService;

class MethodFacts {
	public function __construct(
		public string $module,
		public string $name,
		public string $function,
		public string $className,
		public string $classMethodName,
		public ?string $requiredClassRight,
		public ?string $requiredMethodRight
	) {}
}
