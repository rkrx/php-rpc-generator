<?php

namespace RpcGenerator\Generation;

use DvTeam\JSON\JSON;
use RpcGenerator\Common\ClassFacts\ClassDefinition;
use RpcGenerator\Common\ClassFacts\ParameterDefinition;
use RpcGenerator\Common\ClassFacts\TypeDefinition;
use RpcGenerator\Common\StringUtils;
use RpcGenerator\Generation\Common\LinkInterface;

/**
 * {@see JsESMGeneratorStrategyTest}

 */
class JsESMGeneratorStrategy implements GeneratorStrategyInterface {
	public function __construct(private LinkInterface $linkGenerator) {}

	public function generate(ClassDefinition $classDefinition): string {
		$functions = [];

		foreach($classDefinition->methods as $methodDefinition) {
			$link = $this->linkGenerator->generateLink($classDefinition, $methodDefinition);

			$jsonLink = self::stringify($link);
			$target = self::stringify([explode('\\', $classDefinition->fqClassName), $methodDefinition->methodName]);
			$json = static fn($data) => self::stringify($data);

			$jsDoc = $this->makeJsDoc($methodDefinition->parameters, $methodDefinition->return);

			$body = "
				async {$methodDefinition->name}(params) {
					if(typeof params === 'object' && !Array.isArray(params) && params !== null) {
						throw new Error('The params argument must be an object');
					}
					try {
						const response = await fetch({$jsonLink}, {
							method: {$json('POST')},
							headers: {$json(['Content-Type' => 'application/json', 'accept' => 'application/json'])},
							body: JSON.stringify({target: {$target}, params: params})
						});

						// Check if the response status is in the 4xx or 5xx range
						if (!response.ok) {
							// Handling 4xx Client Errors
							if (response.status >= 400 && response.status < 500) {
								const errorDetails = await response.json();
								return Promise.reject({
									status: response.status,
									message: response.statusText,
									details: errorDetails
								});
							}
							// Handling 5xx Server Errors
							else if (response.status >= 500 && response.status < 600) {
								const errorDetails = await response.json();
								return Promise.reject({
									status: response.status,
									message: response.statusText,
									details: errorDetails
								});
							}
						}

						// If the response is okay (2xx status), parse and return the JSON
						return await response.json();
					} catch (error) {
						return Promise.reject({
							status: 'unknown',
							message: error.message,
							details: null
						});
					}
				}
			";

			$jsDoc = StringUtils::addIndentation($jsDoc);

			$body = StringUtils::stripMargin($body);
			$body = StringUtils::addIndentation($body);

			$functions[] = sprintf("%s\n%s", $jsDoc, $body);
		}

		$classBody = [];
		$classBody[] = "export default {";
		$classBody = [...$classBody, implode(",\n\n", $functions)];
		$classBody[] = "};";

		return sprintf("%s\n", implode("\n\n", $classBody));
	}

	/**
	 * @param array<ParameterDefinition> $parameters
	 * @param TypeDefinition|null $return
	 * @return string
	 */
	private function makeJsDoc(array $parameters, TypeDefinition|null $return) {
		$result = ['/**'];
		$objProperties = [];
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
			$objProperties[] = sprintf('%s: %s', $parameter->name, $typeStr);
		}

		// Use extra spaces to be compatible with template engines that use {{ and }} as delimiters
		$result[] = sprintf(' * @param { { %s } } params', implode(', ', $objProperties));
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
				$result[] = sprintf(' * @return { Promise<%s> }', $typeStr);
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

	private static function stringify($input) {
		$result = JSON::stringify($input);
		return strtr($result, ["'" => "\\'", '"' => "'"]);
	}
}
