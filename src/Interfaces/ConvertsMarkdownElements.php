<?php declare(strict_types=1);

namespace hollodotme\Markdown\Interfaces;

interface ConvertsMarkdownElements
{
	public function convertElement(RepresentsMarkdownElement $markdownElement) : string;
}