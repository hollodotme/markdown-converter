<?php declare(strict_types=1);

namespace hollodotme\Markdown;

abstract class TokenType
{
	public const BlockElement  = 'block';

	public const InlineElement = 'inline';

	public const Text          = 'text';

	public const Whitespace    = 'whitespace';

	public const Tab           = 'tab';
}