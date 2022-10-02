<?php

namespace RpcGenerator\Common;

use Kir\ClassFinder\ClassFinder;

class ClassFinderAdapter implements ClassFinderInterface {
	public function findAllClassesInFile(string $filename): array {
		$classNames = ClassFinder::findClassesFromIterableFileList([$filename]);
		return iterator_to_array($classNames, false);
	}
}