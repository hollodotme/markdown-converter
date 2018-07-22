<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;

final class LineBreak implements RepresentsMarkdownElement
{
	public function getName() : string
	{
		return BlockElement::LINE_BREAK;
	}
}