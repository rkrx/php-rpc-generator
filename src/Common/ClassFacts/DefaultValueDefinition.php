<?php

namespace RpcGenerator\Common\ClassFacts;

class DefaultValueDefinition {
	/**
	 * @param bool $has
	 * @param mixed $value
	 */
	public function __construct(
		public bool $has,
		public mixed $value,
	) {}
}