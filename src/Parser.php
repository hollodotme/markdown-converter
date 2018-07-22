<?php declare(strict_types=1);

namespace hollodotme\Markdown;

use Generator;
use hollodotme\Markdown\BlockElements\BlankLine;
use hollodotme\Markdown\BlockElements\Blockquote;
use hollodotme\Markdown\BlockElements\Code;
use hollodotme\Markdown\BlockElements\Header;
use hollodotme\Markdown\BlockElements\HorizontalRule;
use hollodotme\Markdown\BlockElements\LineBreak;
use hollodotme\Markdown\BlockElements\SortedListItem;
use hollodotme\Markdown\BlockElements\UnsortedListItem;
use hollodotme\Markdown\Interfaces\ParsesMarkdown;
use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
use function array_filter;
use function floor;
use function preg_match;
use function strlen;

final class Parser implements ParsesMarkdown
{
	/**
	 * @param string $line
	 *
	 * @return Generator|RepresentsMarkdownElement[]
	 */
	public function getBlockElements( string $line ) : Generator
	{
		$elements = [];

		$elements[] = $this->getHeaderElement( $line );
		$elements[] = $this->getHorizontalRule( $line );
		$elements[] = $this->getUnsortedListItem( $line );
		$elements[] = $this->getSortedListItem( $line );
		$elements[] = $this->getBlockquote( $line );
		$elements[] = $this->getCode( $line );
		$elements[] = $this->getLineBreak( $line );
		$elements[] = $this->getBlankLine( $line );

		yield from array_values( array_filter( $elements ) );
	}

	private function getHeaderElement( string $line ) : ?Header
	{
		if ( !preg_match( '/^(#+)\s+(.+)/', $line, $matches ) )
		{
			return null;
		}

		$level    = strlen( $matches[1] );
		$contents = trim( $matches[2] );

		return new Header( $contents, $level );
	}

	private function getUnsortedListItem( string $line ) : ?UnsortedListItem
	{
		if ( !preg_match( '#^(?:(\s+)?)[*-]\s+(.+)#', $line, $matches ) )
		{
			return null;
		}

		$indentLevel = (int)floor( strlen( $matches[1] ) / 2 ) + 1;
		$contents    = trim( $matches[2] );

		return new UnsortedListItem( $contents, $indentLevel );
	}

	private function getSortedListItem( string $line ) : ?SortedListItem
	{
		if ( !preg_match( '#^\s*((\d+\.)+)\s+(.+)#', $line, $matches ) )
		{
			return null;
		}

		$numbering = $matches[1];
		$contents  = trim( $matches[3] );

		return new SortedListItem( $contents, $numbering );
	}

	private function getBlockquote( string $line ) : ?Blockquote
	{
		if ( !preg_match( '#^(?:(\s+)?)>\s+(.+)#', $line, $matches ) )
		{
			return null;
		}

		$indentLevel = (int)floor( strlen( $matches[1] ) / 2 ) + 1;
		$contents    = trim( $matches[2] );

		return new Blockquote( $contents, $indentLevel );
	}

	private function getCode( string $line ) : ?Code
	{
		if ( preg_match( '#^(\t{1,})(\S.*)$#', $line, $matches ) )
		{
			$indentLevel = strlen( $matches[1] );
			$contents    = trim( $matches[2] );

			return new Code( $contents, $indentLevel );
		}

		if ( preg_match( '#^( {4,})(\S.*)$#', $line, $matches ) )
		{
			$indentLevel = (int)floor( strlen( $matches[1] ) / 4 );
			$contents    = trim( $matches[2] );

			return new Code( $contents, $indentLevel );
		}

		return null;
	}

	private function getHorizontalRule( string $line ) : ?HorizontalRule
	{
		$cleanLine = preg_replace( '#\s#', '', $line );

		if ( !preg_match( '#^(\-{3,}|\*{3,}|_{3,})$#', $cleanLine ) )
		{
			return null;
		}

		return new HorizontalRule();
	}

	private function getLineBreak( string $line ) : ?LineBreak
	{
		if ( !preg_match( '#\S\s{2,}$#', $line ) )
		{
			return null;
		}

		return new LineBreak();
	}

	private function getBlankLine( string $line ) : ?BlankLine
	{
		if ( !preg_match( '#^\s*$#', $line ) )
		{
			return null;
		}

		return new BlankLine();
	}
}