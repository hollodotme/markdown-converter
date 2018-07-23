<?php declare(strict_types=1);

namespace hollodotme\Markdown;

final class TokenCollection
{
	private $tokens = [];

	public function add( Token $token ) : void
	{
		$this->tokens[] = $token;
	}
}