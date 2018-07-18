<?php declare(strict_types=1);

namespace hollodotme\Markdown\Interfaces;

interface RepresentsMarkdownElement
{
	public function getName() : string;

	public function isMultiline() : bool;
}