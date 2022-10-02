<?php

namespace RpcGenerator\Common;

interface ClassFinderInterface {
	/**
	 * @param string $filename
	 * @return array<string>
	 */
	public function findAllClassesInFile(string $filename): array;
}