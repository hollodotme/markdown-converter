<?php declare(strict_types=1);

namespace hollodotme\Markdown;

use Generator;
use hollodotme\Markdown\Exceptions\RuntimeException;
use hollodotme\Markdown\Interfaces\ConvertsMarkdownElements;
use hollodotme\Markdown\Interfaces\ParsesMarkdown;
use hollodotme\Markdown\Interfaces\RepresentsMarkdownElement;
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
		if ( false !== $this->sourceStream )
		{
			@fclose( $this->sourceStream );
		}

		if ( false !== $this->targetStream )
		{
			@fclose( $this->targetStream );
		}
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
		$sourceStream = @fopen( $sourceFile, 'rb' );

		if ( false === $sourceStream )
		{
			throw new RuntimeException( 'Could not open source stream from file.' );
		}

		$this->sourceStream = $sourceStream;
	}

	/**
	 * @throws RuntimeException
	 */
	private function initTargetStream() : void
	{
		$targetStream = @fopen( 'php://temp', 'wb' );

		if ( false === $targetStream )
		{
			throw new RuntimeException( 'Could not open temporary target stream.' );
		}

		$this->targetStream = $targetStream;
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

	/**
	 * @return Generator|RepresentsMarkdownElement[]
	 */
	private function getElementsFromSourceStream() : Generator
	{
		while ( $line = stream_get_line( $this->sourceStream, 2048 ) )
		{
			yield from $this->parser->getBlockElements( $line );
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

	/**
	 * @param string $targetFile
	 *
	 * @throws RuntimeException
	 * @return int
	 */
	private function copyTargetStreamToFile( string $targetFile ) : int
	{
		$targetFileHandle = @fopen( $targetFile, 'wb' );

		if ( false === $targetFileHandle )
		{
			throw new RuntimeException( 'Could not open target file.' );
		}

		return (int)stream_copy_to_stream( $this->targetStream, $targetFileHandle );
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
		$sourceStream = @fopen( 'data:text/plain' . urlencode( $markdown ), 'rb' );

		if ( false === $sourceStream )
		{
			throw new RuntimeException( 'Could not open source stream from string.' );
		}

		$this->sourceStream = $sourceStream;
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