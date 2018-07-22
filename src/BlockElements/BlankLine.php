<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

use hollodotme\Markdown\Exceptions\LineMismatchException;
use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
use function preg_match;

final class BlankLine implements RepresentsMarkdownElement
{
	public static function fromLine( string $line ) : self
	{
		if ( !preg_match( '#^\s*$#', $line ) )
		{
			throw new LineMismatchException( 'Line does not match blank line.' );
		}

		return new self();
	}

	public function getName() : string
	{
		return BlockElement::BLANK_LINE;
	}
}