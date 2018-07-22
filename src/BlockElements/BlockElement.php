<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

abstract class BlockElement
{
	public const HEADER             = 'header';

	public const UNSORTED_LIST_ITEM = 'unsorted list item';

	public const SORTED_LIST_ITEM   = 'sorted list item';

	public const QUOTE              = 'quote';

	public const HORIZONTAL_RULE    = 'horizontal rule';

	public const LINE_BREAK         = 'line break';

	public const BLANK_LINE         = 'blank line';

	public const CODE               = 'code';
}