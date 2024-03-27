<?php

namespace RpcGenerator\Common;

use RpcGenerator\Common\ClassFacts\ClassDefinition;

class ClassGenerationResult {
	public function __construct(
		public ClassDefinition $def,
		public bool $unmodified,
		public ?string $body
	) {}
}
