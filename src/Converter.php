<?php declare(strict_types=1);

namespace hollodotme\Markdown;

use Generator;
use hollodotme\Markdown\Exceptions\RuntimeException;
use hollodotme\Markdown\Interfaces\ConvertsMarkdownElements;
use hollodotme\Markdown\Interfaces\ParsesMarkdown;
use function fclose;
use function fopen;
use function fwrite;
use function stream_copy_to_stream;
use function stream_get_contents;
use function stream_get_line;
use function urlencode;

final class Converter
{
	/** @var ParsesMarkdown */
	private $parser;

	/** @var ConvertsMarkdownElements */
	private $targetConverter;

	/** @var resource */
	private $sourceStream;

	/** @var resource */
	private $targetStream;

	/**
	 * @param ParsesMarkdown           $parser
	 * @param ConvertsMarkdownElements $targetConverter
	 */
	public function __construct(
		ParsesMarkdown $parser,
		ConvertsMarkdownElements $targetConverter
	)
	{
		$this->parser          = $parser;
		$this->targetConverter = $targetConverter;
	}

	public function __destruct()
	{
		@fclose( $this->sourceStream );
		@fclose( $this->targetStream );
	}

	/**
	 * @param string $sourceFile
	 *
	 * @throws RuntimeException
	 * @return string
	 */
	public function convertFileToString( string $sourceFile ) : string
	{
		$this->initSourceStreamFromFile( $sourceFile );

		$this->convertSourceToTargetStream();

		return $this->getContentsFromTargetStream();
	}

	/**
	 * @param string $sourceFile
	 *
	 * @throws RuntimeException
	 */
	private function initSourceStreamFromFile( string $sourceFile ) : void
	{
		$this->sourceStream = @fopen( $sourceFile, 'rb' );

		$this->guardSourceStreamIsValid();
	}

	/**
	 * @throws RuntimeException
	 */
	private function guardSourceStreamIsValid() : void
	{
		if ( false === $this->sourceStream )
		{
			throw new RuntimeException( 'Could not open source stream.' );
		}
	}

	/**
	 * @throws RuntimeException
	 */
	private function initTargetStream() : void
	{
		$this->targetStream = fopen( 'php://temp', 'wb' );

		if ( false === $this->targetStream )
		{
			throw new RuntimeException( 'Could not open target stream.' );
		}
	}

	/**
	 * @throws RuntimeException
	 */
	private function convertSourceToTargetStream() : void
	{
		$this->initTargetStream();

		foreach ( $this->getElementsFromSourceStream() as $element )
		{
			$convertedElement = $this->targetConverter->convertElement( $element );

			$this->writeConvertedElementToTargetStream( $convertedElement );
		}
	}

	private function getElementsFromSourceStream() : Generator
	{
		while ( $line = stream_get_line( $this->sourceStream, 2048 ) )
		{
			yield from $this->parser->getElements( $line );
		}
	}

	/**
	 * @param string $convertedElement
	 *
	 * @throws RuntimeException
	 */
	private function writeConvertedElementToTargetStream( string $convertedElement ) : void
	{
		if ( '' === $convertedElement )
		{
			return;
		}

		$written = fwrite( $this->targetStream, $convertedElement );

		if ( false === $written )
		{
			throw new RuntimeException( 'Could not write to target stream.' );
		}
	}

	private function getContentsFromTargetStream() : string
	{
		return stream_get_contents( $this->targetStream );
	}

	/**
	 * @param string $sourceFile
	 * @param string $targetFile
	 *
	 * @throws RuntimeException
	 * @return int
	 */
	public function convertFileToFile( string $sourceFile, string $targetFile ) : int
	{
		$this->initSourceStreamFromFile( $sourceFile );

		$this->convertSourceToTargetStream();

		return $this->copyTargetStreamToFile( $targetFile );
	}

	private function copyTargetStreamToFile( string $targetFile ) : int
	{
		$targetFileHandle = fopen( $targetFile, 'wb' );

		return stream_copy_to_stream( $this->targetStream, $targetFileHandle );
	}

	/**
	 * @param string $markdown
	 *
	 * @throws RuntimeException
	 * @return string
	 */
	public function convertStringToString( string $markdown ) : string
	{
		$this->initSourceStreamFromString( $markdown );

		$this->convertSourceToTargetStream();

		return $this->getContentsFromTargetStream();
	}

	/**
	 * @param string $markdown
	 *
	 * @throws RuntimeException
	 */
	private function initSourceStreamFromString( string $markdown ) : void
	{
		$this->sourceStream = @fopen( 'data:text/plain' . urlencode( $markdown ), 'rb' );

		$this->guardSourceStreamIsValid();
	}

	/**
	 * @param string $markdown
	 * @param string $targetFile
	 *
	 * @throws RuntimeException
	 * @return int
	 */
	public function convertStringToFile( string $markdown, string $targetFile ) : int
	{
		$this->initSourceStreamFromString( $markdown );

		$this->convertSourceToTargetStream();

		return $this->copyTargetStreamToFile( $targetFile );
	}
}