<?php declare(strict_types=1);

namespace hollodotme\Markdown\Elements;

use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;

final class Header implements RepresentsMarkdownElement
{
	/** @var string */
	private $contents;

	/** @var int */
	private $level;

	/**
	 * @param string $contents
	 * @param int    $level
	 */
	public function __construct( string $contents, int $level )
	{
		$this->contents = $contents;
		$this->level    = $level;
	}

	public function getName() : string
	{
		return Element::HEADER;
	}

	public function getContents() : string
	{
		return $this->contents;
	}

	public function getLevel() : int
	{
		return $this->level;
	}
}