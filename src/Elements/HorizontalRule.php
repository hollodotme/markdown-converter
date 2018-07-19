<?php declare(strict_types=1);

namespace hollodotme\Markdown\Elements;

use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;

final class HorizontalRule implements RepresentsMarkdownElement
{
	public function getName() : string
	{
		return Element::HORIZONTAL_RULE;
	}

	public function isMultiline() : bool
	{
		return false;
	}
}