<?php declare(strict_types=1);

namespace hollodotme\Markdown\Interfaces;

use Generator;

interface ParsesMarkdown
{
	/**
	 * @param string $line
	 *
	 * @return Generator|RepresentsMarkdownElement[]
	 */
	public function getElements(string $line) : Generator;
}