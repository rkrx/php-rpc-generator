<?php

namespace RpcGenerator;

use ReflectionClass;
use RpcGenerator\Attributes\RPCClass;
use RpcGenerator\Attributes\RPCMethod;
use RpcGenerator\RPCFactService\ClassAttributeNotFoundException;
use RpcGenerator\RPCFactService\ClassMethodAttributeNotFoundException;
use RpcGenerator\RPCFactService\ClassMethodNotFoundException;
use RpcGenerator\RPCFactService\ClassNotFoundException;
use RpcGenerator\RPCFactService\MethodFacts;

/**
 * {@see RPCClassMethodFactProviderTest}
 */
class RPCClassMethodFactProvider {
	/**
	 * @param string $className
	 * @param string $methodName
	 * @return MethodFacts
	 * @throws ClassNotFoundException
	 * @throws ClassAttributeNotFoundException
	 * @throws ClassMethodNotFoundException
	 * @throws ClassMethodAttributeNotFoundException
	 */
	public static function getMethodFacts(string $className, string $methodName): MethodFacts {
		try {
			$reflectionClass = new ReflectionClass($className);
		} catch (\ReflectionException) {
			$sanitizedClassName = self::sanitizeString($className);
			throw new ClassNotFoundException("Class {$sanitizedClassName} does not exist");
		}

		$reflectionClassAttribute = $reflectionClass->getAttributes(RPCClass::class)[0]
			?? throw new ClassAttributeNotFoundException('Class does not have RPCClass attribute');

		/** @var RPCClass $classAttribute */
		$classAttribute = $reflectionClassAttribute->newInstance();

		try {
			$reflectionMethod = $reflectionClass->getMethod($methodName);
		} catch (\ReflectionException) {
			$sanitizedClassName = self::sanitizeString($className);
			$sanitizedMethodName = self::sanitizeString($methodName);
			throw new ClassMethodNotFoundException("Method {$sanitizedClassName}::{$sanitizedMethodName} does not exist");
		}

		$reflectionMethodAttribute = $reflectionMethod->getAttributes(RPCMethod::class)[0]
			?? throw new ClassMethodAttributeNotFoundException('Class does not have RPCClass attribute');

		/** @var RPCMethod $methodAttribute */
		$methodAttribute = $reflectionMethodAttribute->newInstance();

		return new MethodFacts(
			module: $classAttribute->module,
			name: $classAttribute->name ?? $reflectionClass->getShortName(),
			function: $methodAttribute->name ?? $reflectionMethod->getName(),
			className: $reflectionClass->getName(),
			classMethodName: $reflectionMethod->getName(),
			requiredClassRight: $classAttribute->requiredRight,
			requiredMethodRight: $methodAttribute->requiredRight
		);
	}

	private static function sanitizeString(string $name) {
		return preg_replace("{[^\\w\\\\]+}", '', $name);
	}
}
