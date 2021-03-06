<?php declare(strict_types=1);

namespace hollodotme\Markdown\BlockElements;

use hollodotme\Markdown\Exceptions\LineMismatchException;
use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
use function floor;
use function preg_match;
use function strlen;
use function trim;

final class Code implements RepresentsMarkdownElement
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
	 * @return Code
	 */
	public static function fromLine( string $line ) : self
	{
		if ( preg_match( '#^(\t{1,})(\S.*)$#', $line, $matches ) )
		{
			$indentLevel = strlen( $matches[1] );
			$contents    = trim( $matches[2] );

			return new self( $contents, $indentLevel );
		}

		if ( preg_match( '#^( {4,})(\S.*)$#', $line, $matches ) )
		{
			$indentLevel = (int)floor( strlen( $matches[1] ) / 4 );
			$contents    = trim( $matches[2] );

			return new self( $contents, $indentLevel );
		}

		throw new LineMismatchException( 'Line does not match code.' );
	}

	public function getName() : string
	{
		return BlockElement::CODE;
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