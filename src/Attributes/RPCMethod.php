<?php

namespace RpcGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RPCMethod {
	/**
	 * @param string|null $name The name of the entry's method on the client side. If `null` then take the name of the actual method
	 * @param string|null $requiredRight The right required to call this method
	 */
	public function __construct(
		public ?string $name = null,
		public ?string $requiredRight = null,
	) {}
}
