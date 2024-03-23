<?php

namespace RpcGenerator\Generation;

use PHPUnit\Framework\TestCase;
use RpcGenerator\Common\ClassFacts\AtomarTypeDefinition;
use RpcGenerator\Common\ClassFacts\ClassDefinition;
use RpcGenerator\Common\ClassFacts\MethodDefinition;
use RpcGenerator\Common\ClassFacts\TypeDefinition;
use RpcGenerator\Entities\CustomerService;
use RpcGenerator\Generation\Common\LinkInterface;

/**
 * {@see JsESMGeneratorStrategy}
 */
class JsESMGeneratorStrategyTest extends TestCase {
	public function testGenerate() {
		$linkInterface = new class implements LinkInterface {
			public function generateLink(ClassDefinition $classDefinition, MethodDefinition $methodDefinition): string {
				return '/rpc';
			}
		};

		$classDefinition = new ClassDefinition(
			module: 'orders',
			name: 'Orders',
			fqClassName: CustomerService::class,
			className: 'CustomerService',
			methods: [
				new MethodDefinition(
					name: 'getAll',
					methodName: 'getCustomers',
					parameters: [],
					return: new TypeDefinition(
						nullable: false,
						types: [new AtomarTypeDefinition('array', true)]
					)
				)
			]
		);

		$stategy = new JsESMGeneratorStrategy(linkGenerator: $linkInterface);
		$result = $stategy->generate($classDefinition);

		$normalizeWhitespace = static fn(string $str) => preg_replace('/\s+/', ' ', trim($str));

		$expected = '
			export const Orders = {

				/**
				 * @return {Promise<Array>}
				 */
				async getAll() {
					const response = await fetch("/rpc", {
						method: "POST",
						headers: {"Content-Type":"application/json","accept":"application/json"},
						body: JSON.stringify({target: [["RpcGenerator","Entities","CustomerService"],"getCustomers"], params: {}}),
					});

					return await response.json();
				}

			}

		';

		self::assertEquals($normalizeWhitespace($expected), $normalizeWhitespace($result));
	}
}
