<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

use hollodotme\Markdown\Exceptions\LineMismatchException;
use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
use function floor;
use function strlen;
use function trim;

final class Quote implements RepresentsMarkdownElement
{
	/** @var string */
	private $contents;

	/** @var int */
	private $indentLevel;

	private function __construct( string $contents, int $indentLevel )
	{
		$this->contents    = $contents;
		$this->indentLevel = $indentLevel;
	}

	/**
	 * @param string $line
	 *
	 * @throws LineMismatchException
	 * @return Quote
	 */
	public static function fromLine( string $line ) : self
	{
		if ( !preg_match( '#^(?:(\s+)?)>\s+(.+)#', $line, $matches ) )
		{
			throw new LineMismatchException( 'Line does not match quote.' );
		}

		$indentLevel = (int)floor( strlen( $matches[1] ) / 2 ) + 1;
		$contents    = trim( $matches[2] );

		return new self( $contents, $indentLevel );
	}

	public function getName() : string
	{
		return BlockElement::QUOTE;
	}

	public function getContents() : string
	{
		return $this->contents;
	}

	public function getIndentLevel() : int
	{
		return $this->indentLevel;
	}
}