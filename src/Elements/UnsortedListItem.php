<?php declare(strict_types=1);

namespace hollodotme\Markdown\Elements;

use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;

final class UnsortedListItem implements RepresentsMarkdownElement
{
	/** @var string */
	private $contents;

	/** @var int */
	private $indentLevel;

	public function __construct( string $contents, int $indentLevel )
	{
		$this->contents    = $contents;
		$this->indentLevel = $indentLevel;
	}

	public function getName() : string
	{
		return BlockElement::UNSORTED_LIST_ITEM;
	}

	public function getContents() : string
	{
		return $this->contents;
	}

	public function getIndentLevel() : int
	{
		return $this->indentLevel;
	}
}