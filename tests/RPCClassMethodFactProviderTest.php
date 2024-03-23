<?php

namespace RpcGenerator;

use PHPUnit\Framework\TestCase;
use RpcGenerator\Entities\CustomerService;
use RpcGenerator\RPCFactService\MethodFacts;

/**
 * {@see RPCClassMethodFactProvider}
 */
class RPCClassMethodFactProviderTest extends TestCase {
	public function testGetMethodFacts(): void {
		$facts = RPCClassMethodFactProvider::getMethodFacts(CustomerService::class, 'getAll');

		$expected = new MethodFacts(
			module: 'customers',
			name: 'CustomerService',
			function: 'getCustomers',
			className: 'CustomerService',
			classMethodName: 'getAll',
			requiredClassRight: 'customers',
			requiredMethodRight: null
		);

		self::assertEquals($expected, $facts);
	}
}
