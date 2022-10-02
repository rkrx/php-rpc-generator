<?php

namespace RpcGenerator\Common\ClassFacts;

class AtomarTypeDefinition {
	/**
	 * @param string $name
	 * @param bool $builtIn
	 */
	public function __construct(
		public string $name,
		public bool $builtIn,
	) {}
}
