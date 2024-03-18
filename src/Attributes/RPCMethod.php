<?php

namespace RpcGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RPCMethod {
	public function __construct(
		/** @var string $name The name of the entry's method on the client side. If `null` then take the name of the actual method */
		public ?string $name = null,
	) {}
}
