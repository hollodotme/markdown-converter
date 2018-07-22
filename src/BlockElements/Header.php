<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

use hollodotme\Markdown\Exceptions\LineMismatchException;
use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
use function strlen;

final class Header implements RepresentsMarkdownElement
{
	/** @var string */
	private $contents;

	/** @var int */
	private $level;

	private function __construct( string $contents, int $level )
	{
		$this->contents = $contents;
		$this->level    = $level;
	}

	/**
	 * @param string $line
	 *
	 * @throws LineMismatchException
	 * @return Header
	 */
	public static function fromLine( string $line ) : self
	{
		if ( !preg_match( '/^(#+)\s+(.+)/', $line, $matches ) )
		{
			throw new LineMismatchException( 'Line does not match header.' );
		}

		$level    = strlen( $matches[1] );
		$contents = trim( $matches[2] );

		return new self( $contents, $level );
	}

	public function getName() : string
	{
		return BlockElement::HEADER;
	}

	public function getContents() : string
	{
		return $this->contents;
	}

	public function getLevel() : int
	{
		return $this->level;
	}
}