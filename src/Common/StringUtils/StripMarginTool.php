<?php

namespace RpcGenerator\Common\StringUtils;

use RuntimeException;

class StripMarginTool {
	/**
	 * @param string $content
	 * @param int $tabWidth
	 * @param bool $preferSpacesOverTabs
	 * @return string
	 */
	public static function stripMargin(string $content, int $tabWidth = 4, bool $preferSpacesOverTabs = true): string {
		$lines = preg_split('{\r?\n}', $content);

		if($lines === false) {
			throw new RuntimeException('Failed to split string');
		}

		// Strip empty lines at the beginning
		do {
			$firstLine = $lines[0] ?? null;
			if($firstLine === null) {
				break;
			}

			if(trim($firstLine) === '') {
				array_shift($lines);
				continue;
			}

			break;
		} while (true);

		// Strip empty lines at the end
		do {
			$lastLine = $lines[count($lines) - 1] ?? null;
			if($lastLine === null) {
				break;
			}

			if(trim($lastLine) === '') {
				array_pop($lines);
				continue;
			}

			break;
		} while (true);

		$min = null;
		foreach($lines as $line) {
			$count = self::countLeadingSpaces($line, $tabWidth);

			if($count !== null) {
				$totalTabs = (int) round($count / $tabWidth);
				$min = $min === null ? $totalTabs : min($min, $totalTabs);
			}
		}

		$result = [];
		foreach($lines as $line) {
			$spaces = self::countLeadingSpaces($line, $tabWidth);
			$totalTabs = (int) round($spaces / $tabWidth);
			$remainingTabs = $totalTabs - $min;
			if($preferSpacesOverTabs) {
				$spacePrefix = $remainingTabs > 0 ? str_repeat("\t", $remainingTabs) : '';
			} else {
				$spacePrefix = $remainingTabs > 0 ? str_repeat(' ', $remainingTabs * $tabWidth) : '';
			}
			$result[] = sprintf('%s%s', $spacePrefix, ltrim($line));
		}

		return implode("\n", $result);
	}

	/**
	 * @param string $line
	 * @param int $tabWidth
	 * @return int|null
	 */
	private static function countLeadingSpaces(string $line, int $tabWidth): ?int {
		$count = null;

		if(preg_match('{^(\\s+)}', $line, $matches)) {
			$whitespaceStr = $matches[1];
			$whitespaces = preg_split('{}', $whitespaceStr);
			$count = 0;

			foreach($whitespaces as $whitespace) {
				// Tab counts as $tabWidth spaces
				if($whitespace === "\t") {
					$count += $tabWidth;
				} elseif($whitespace === ' ') {
					$count++;
				}
			}
		}

		return $count;
	}
}
