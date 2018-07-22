<?php declare(strict_types=1);

namespace hollodotme\Markdown\Elements;

use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;

final class BlankLine implements RepresentsMarkdownElement
{
	public function getName() : string
	{
		return Element::BLANK_LINE;
	}
}