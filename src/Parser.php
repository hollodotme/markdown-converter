<?php declare(strict_types=1);

namespace hollodotme\Markdown;

use Generator;
use hollodotme\Markdown\BlockElements\BlankLine;
use hollodotme\Markdown\BlockElements\Code;
use hollodotme\Markdown\BlockElements\Header;
use hollodotme\Markdown\BlockElements\HorizontalRule;
use hollodotme\Markdown\BlockElements\LineBreak;
use hollodotme\Markdown\BlockElements\Quote;
use hollodotme\Markdown\BlockElements\SortedListItem;
use hollodotme\Markdown\BlockElements\UnsortedListItem;
use hollodotme\Markdown\Exceptions\LineMismatchException;
use hollodotme\Markdown\Interfaces\ParsesMarkdown;
use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;

final class Parser implements ParsesMarkdown
{
	private const BLOCK_ELEMENTS = [
		Header::class,
		HorizontalRule::class,
		UnsortedListItem::class,
		SortedListItem::class,
		Quote::class,
		Code::class,
		BlankLine::class,
	];

	/**
	 * @param string $line
	 *
	 * @return Generator|RepresentsMarkdownElement[]
	 */
	public function getBlockElements( string $line ) : Generator
	{
		foreach ( self::BLOCK_ELEMENTS as $blockElementClass )
		{
			$list = $this->getElementList( $blockElementClass, $line );
			if ( [] !== $list )
			{
				yield from $list;
				break;
			}
		}

		yield from $this->getElementList( LineBreak::class, $line );
	}

	private function getElementList( string $blockElementClass, string $line ) : array
	{
		try
		{
			/** @var RepresentsMarkdownElement $blockElementClass */
			return [$blockElementClass::fromLine( $line )];
		}
		catch ( LineMismatchException $e )
		{
			return [];
		}
	}
}