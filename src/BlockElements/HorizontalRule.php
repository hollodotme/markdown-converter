<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

use hollodotme\Markdown\Exceptions\LineMismatchException;
use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
use function preg_match;
use function preg_replace;

final class HorizontalRule implements RepresentsMarkdownElement
{
	public static function fromLine( string $line ) : self
	{
		$cleanLine = preg_replace( '#\s#', '', $line );

		if ( !preg_match( '#^(\-{3,}|\*{3,}|_{3,})$#', $cleanLine ) )
		{
			throw new LineMismatchException( 'Line does not match horizontal rule.' );
		}

		return new self();
	}

	public function getName() : string
	{
		return BlockElement::HORIZONTAL_RULE;
	}
}