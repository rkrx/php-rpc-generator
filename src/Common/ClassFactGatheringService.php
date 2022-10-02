<?php

namespace RpcGenerator\Common;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use RpcGenerator\Attributes\RPCClass;
use RpcGenerator\Attributes\RPCMethod;
use RpcGenerator\Common\ClassFacts\ClassDefinition;
use RpcGenerator\Common\ClassFacts\MethodDefinition;
use RpcGenerator\Common\ClassFacts\AtomarTypeDefinition;
use RpcGenerator\Common\ClassFacts\DefaultValueDefinition;
use RpcGenerator\Common\ClassFacts\ParameterDefinition;
use RpcGenerator\Common\ClassFacts\TypeDefinition;
use RuntimeException;

class ClassFactGatheringService {
	/**
	 * @param string $className
	 * @return ClassDefinition
	 * @throws \ReflectionException
	 */
	public static function getFacts(string $className): ClassDefinition {
		$refClass = new ReflectionClass($className);

		$module = null;
		$classShortName = $className = $refClass->getShortName();
		$fqClassName = $refClass->getName();

		$refAttributes = $refClass->getAttributes(RPCClass::class);
		foreach($refAttributes as $refAttribute) {
			/** @var RPCClass $attribute */
			$attribute = $refAttribute->newInstance();
			$classShortName = $attribute->name ?? $classShortName;
			$module = $attribute->module;
		}

		$methods = [];
		foreach($refClass->getMethods() as $refMethod) {
			$refAttributes = $refMethod->getAttributes(RPCMethod::class);

			$parameters = [];
			$methodName = $refMethod->getName();

			foreach($refAttributes as $refAttribute) {
				/** @var RPCMethod $attribute */
				$attribute = $refAttribute->newInstance();
				$parameters = array_map(static fn($p) => self::transformParameter($attribute, $p), $refMethod->getParameters());
				$methodName = $attribute->name ?? $methodName;
			}

			$methods[] = new MethodDefinition(
				name: $methodName,
				methodName: $refMethod->getName(),
				parameters: $parameters,
				return: $refMethod->getReturnType() !== null ? self::getTypes($refMethod->getReturnType()) : null
			);
		}

		return new ClassDefinition(
			module: $module,
			name: $classShortName,
			fqClassName: $fqClassName,
			className: $className,
			methods: $methods
		);
	}

	/**
	 * @param ReflectionType $reflType
	 * @return TypeDefinition
	 */
	private static function getTypes(ReflectionType $reflType): TypeDefinition {
		if($reflType instanceof ReflectionUnionType) {
			$types = [];
			foreach($reflType->getTypes() as $type) {
				$types[] = new AtomarTypeDefinition(name: $type->getName(), builtIn: $type->isBuiltin());
			}
			return new TypeDefinition(
				nullable: false,
				types: $types
			);
		}

		if($reflType instanceof ReflectionNamedType) {
			return new TypeDefinition(
				nullable: $reflType->allowsNull(),
				types: [new AtomarTypeDefinition(name: $reflType->getName(), builtIn: $reflType->isBuiltin())]
			);
		}

		throw new RuntimeException(sprintf("Unknown type of parameter-type found: %s", get_class($reflType)));
	}

	private static function transformParameter(RPCMethod $methodDef, ReflectionParameter $reflParameter): ParameterDefinition {
		$reflType = $reflParameter->getType();

		return new ParameterDefinition(
			name: $reflParameter->getName(),
			nullable: $reflParameter->allowsNull(),
			typing: self::getTypes($reflType),
			default: new DefaultValueDefinition(
				has: $reflParameter->isDefaultValueAvailable(),
				value: $reflParameter->isDefaultValueAvailable() ? $reflParameter->getDefaultValue() : null
			)
		);
	}
}
