<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

use hollodotme\Markdown\Exceptions\LineMismatchException;
use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
use function floor;
use function preg_match;
use function preg_replace;
use function strlen;
use function trim;

final class UnsortedListItem implements RepresentsMarkdownElement
{
	/** @var string */
	private $contents;

	/** @var int */
	private $indentLevel;

	private function __construct( string $contents, int $indentLevel )
	{
		$this->contents    = $contents;
		$this->indentLevel = $indentLevel;
	}

	public static function fromLine( string $line ) : self
	{
		$cleanLine = preg_replace( '#\s#', '', $line );

		if ( preg_match( '#^(\-{3,}|\*{3,}|_{3,})$#', $cleanLine ) )
		{
			throw new LineMismatchException( 'Line does not match unsorted list item.' );
		}

		if ( !preg_match( '#^(?:(\s+)?)[*-]\s+(.+)#', $line, $matches ) )
		{
			throw new LineMismatchException( 'Line does not match unsorted list item.' );
		}

		$indentLevel = (int)floor( strlen( $matches[1] ) / 2 ) + 1;
		$contents    = trim( $matches[2] );

		return new self( $contents, $indentLevel );
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