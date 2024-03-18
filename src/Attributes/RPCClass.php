<?php

namespace RpcGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RPCClass {
	/**
	 * @param string $module The module name of the RPC class.
	 * @param string|null $name The name of the entry on the client side. If `null` then take the name of the actual class.
	 * @param string $requiredRight The required right to call the RPC class.
	 */
	public function __construct(
		public string $module,
		public ?string $name,
		public string $requiredRight
	) {}
}
