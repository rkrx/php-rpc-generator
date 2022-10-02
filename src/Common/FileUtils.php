<?php

namespace RpcGenerator\Common;

use RuntimeException;

class FileUtils {
	public static function mkdir(string $path, int $mode = 0777): void {
		if(is_dir($path)) {
			return;
		}
		if(!mkdir(directory: $path, permissions: $mode, recursive: true) && !is_dir($path)) {
			throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
		}
	}
}
