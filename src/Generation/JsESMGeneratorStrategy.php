<?php

namespace RpcGenerator\Generation;

use DvTeam\JSON\JSON;
use RpcGenerator\Common\ClassFacts\ClassDefinition;
use RpcGenerator\Common\ClassFacts\ParameterDefinition;
use RpcGenerator\Common\ClassFacts\TypeDefinition;
use RpcGenerator\Common\StringUtils;
use RpcGenerator\Generation\Common\LinkInterface;

class JsESMGeneratorStrategy implements GeneratorStrategyInterface {
	public function __construct(private LinkInterface $linkGenerator) {}

	public function generate(ClassDefinition $classDefinition): string {
		$functions = [];

		foreach($classDefinition->methods as $methodDefinition) {
			$link = $this->linkGenerator->generateLink($classDefinition, $methodDefinition);

			$jsonLink = JSON::stringify($link);
			$target = JSON::stringify([explode('\\', $classDefinition->fqClassName), $methodDefinition->name]);
			$json = static fn($data) => JSON::stringify($data);

			$paramStr = $this->makeParamStr($methodDefinition->parameters);
			$mappingStr = $this->makeParamMappingStr($methodDefinition->parameters);
			$jsDoc = $this->makeJsDoc($methodDefinition->parameters, $methodDefinition->return);

			$body = "
				async {$methodDefinition->name}({$paramStr}) {
					const response = await fetch({$jsonLink}, {
						method: {$json('POST')},
						headers: {$json(['Content-Type' => 'application/json', 'accept' => 'application/json'])},
						body: JSON.stringify({fn: {$target}, params: $mappingStr}),
					});

					return await response.json();
				}";

			$jsDoc = StringUtils::addIndentation($jsDoc);

			$body = StringUtils::stripMargin($body);
			$body = StringUtils::addIndentation($body);

			$functions[] = sprintf("%s\n%s", $jsDoc, $body);
		}

		$classBody = [];
		$classBody[] = "export const {$classDefinition->name} = {";
		$classBody = [...$classBody, implode(",\n\n", $functions)];
		$classBody[] = "}";

		return sprintf("%s\n", implode("\n\n", $classBody));
	}

	/**
	 * @param array<ParameterDefinition> $parameters
	 * @return string
	 */
	private function makeParamStr(array $parameters): string {
		$result = [];
		foreach($parameters as $parameter) {
			$result[] = $parameter->name;
		}
		return implode(', ', $result);
	}

	/**
	 * @param array<ParameterDefinition> $parameters
	 * @return string
	 */
	private function makeParamMappingStr(array $parameters): string {
		$result = [];
		foreach($parameters as $parameter) {
			$result[] = sprintf('%s: %s', $parameter->name, $parameter->name);
		}
		return sprintf('{%s}', implode(', ', $result));
	}

	/**
	 * @param array<ParameterDefinition> $parameters
	 * @param TypeDefinition|null $return
	 * @return string
	 */
	private function makeJsDoc(array $parameters, TypeDefinition|null $return) {
		$result = ['/**'];
		foreach($parameters as $parameter) {
			$types = [];
			if($parameter->typing->nullable) {
				$types[] = 'null';
			}
			foreach($parameter->typing->types as $type) {
				$types[] = $this->translateType($type->name);
			}
			$types = array_unique($types, SORT_STRING);
			$typeStr = implode('|', $types);
			$result[] = sprintf(' * @param {%s} %s', $typeStr, $parameter->name);
		}

		if($return !== null) {
			$types = [];
			if($return->nullable) {
				$types[] = 'null';
			}
			foreach($return->types as $type) {
				$types[] = $this->translateType($type->name);
			}
			if(count($types)) {
				$types = array_unique($types, SORT_STRING);
				$typeStr = implode('|', $types);
				$result[] = sprintf(' * @return {Promise<%s>}', $typeStr);
			}
		}

		$result[] = ' */';

		return implode("\n", $result);
	}

	private function translateType(string $typeName) {
		return match ($typeName) {
			'array' => 'Array',
			'bool' => 'boolean',
			'int', 'float', 'double' => 'number',
			'string' => 'string',
			default => 'object',
		};
	}
}
