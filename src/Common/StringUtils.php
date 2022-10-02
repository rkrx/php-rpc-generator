<?php

namespace RpcGenerator\Common;

use RpcGenerator\Common\StringUtils\StripMarginTool;

class StringUtils {
	public static function stripMargin(string $content, int $tabWidth = 4, bool $preferSpacesOverTabs = true): string {
		return StripMarginTool::stripMargin($content, $tabWidth, $preferSpacesOverTabs);
	}

	public static function addIndentation(string $content, int $tabWidth = 4, bool $preferSpacesOverTabs = true): string {
		$lines = explode("\n", $content);
		$lines = array_map(static fn($line) => ($preferSpacesOverTabs ? str_repeat(' ', $tabWidth) : "\t") . $line, $lines);
		return implode("\n", $lines);
	}
}
