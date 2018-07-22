<?php declare(strict_types=1);

namespace hollodotme\Markdown\Elements;

use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
use function substr_count;

final class SortedListItem implements RepresentsMarkdownElement
{
	/** @var string */
	private $contents;

	/** @var string */
	private $numbering;

	/** @var int */
	private $indentLevel;

	public function __construct( string $contents, string $numbering )
	{
		$this->contents    = $contents;
		$this->numbering   = $numbering;
		$this->indentLevel = substr_count( $numbering, '.' );
	}

	public function getName() : string
	{
		return BlockElement::SORTED_LIST_ITEM;
	}

	public function getContents() : string
	{
		return $this->contents;
	}

	public function getNumbering() : string
	{
		return $this->numbering;
	}

	public function getIndentLevel() : int
	{
		return $this->indentLevel;
	}
}