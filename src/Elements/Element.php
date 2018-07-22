<?php declare(strict_types=1);

namespace hollodotme\Markdown\Elements;

abstract class Element
{
	public const HEADER             = 'header';

	public const UNSORTED_LIST_ITEM = 'unsorted list item';

	public const SORTED_LIST_ITEM   = 'sorted list item';

	public const BLOCKQUOTE         = 'blockquote';

	public const HORIZONTAL_RULE    = 'horizontal rule';

	public const LINE_BREAK         = 'line break';

	public const BLANK_LINE         = 'blank line';
}