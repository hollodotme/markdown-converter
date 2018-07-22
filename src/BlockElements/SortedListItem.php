<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

use hollodotme\Markdown\Exceptions\LineMismatchException;
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

	private function __construct( string $contents, string $numbering )
	{
		$this->contents    = $contents;
		$this->numbering   = $numbering;
		$this->indentLevel = substr_count( $numbering, '.' );
	}

	/**
	 * @param string $line
	 *
	 * @throws LineMismatchException
	 * @return SortedListItem
	 */
	public static function fromLine( string $line ) : self
	{
		if ( !preg_match( '#^\s*((\d+\.)+)\s+(.+)#', $line, $matches ) )
		{
			throw new LineMismatchException( 'Line does not match sorted list item.' );
		}

		$numbering = $matches[1];
		$contents  = trim( $matches[3] );

		return new self( $contents, $numbering );
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