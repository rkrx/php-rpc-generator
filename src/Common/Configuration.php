<?php

namespace RpcGenerator\Common;

use RpcGenerator\Generation\GeneratorStrategyInterface;

class Configuration {
	/**
	 * @param ClassFinderInterface $classFinder
	 * @param IndexCacheInterface $indexCache
	 * @param string $filePattern
	 */
	public function __construct(
		public ClassFinderInterface $classFinder,
		public IndexCacheInterface $indexCache,
		public GeneratorStrategyInterface $generatorStrategy,
		/** @lang RegExp */
		public string $filePattern = '{\\.php$}'
	) {}
}
