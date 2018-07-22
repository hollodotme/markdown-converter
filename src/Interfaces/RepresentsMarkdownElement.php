<?php declare(strict_types=1);

namespace hollodotme\Markdown\Interfaces;

use hollodotme\Markdown\Exceptions\LineMismatchException;

interface RepresentsMarkdownElement
{
	public function getName() : string;

	/**
	 * @param string $line
	 *
	 * @throws LineMismatchException
	 * @return static
	 */
	public static function fromLine( string $line );
}