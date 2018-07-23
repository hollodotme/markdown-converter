<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
use function trim;

final class Text implements RepresentsMarkdownElement
{
	/** @var string */
	private $contents;

	private function __construct( string $contents )
	{
		$this->contents = $contents;
	}

	public static function fromLine( string $line ) : self
	{
		return new self( trim( $line ) );
	}

	public function getName() : string
	{
		return BlockElement::TEXT;
	}

	public function getContents() : string
	{
		return $this->contents;
	}
}