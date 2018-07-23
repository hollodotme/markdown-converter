<?php declare(strict_types=1);

namespace hollodotme\Markdown;

use hollodotme\Markdown\Exceptions\RuntimeException;
use function mb_strlen;
use function preg_match;

final class Tokenizer
{
	/** @var int */
	private $current;

	/** @var TokenCollection */
	private $tokens;

	public function __construct()
	{
		$this->current = 0;
		$this->tokens  = new TokenCollection();
	}

	/**
	 * @param string $line
	 *
	 * @throws RuntimeException
	 */
	public function tokenizeLine( string $line ) : void
	{
		$tokenizerMethods = [
			'tokenizeWhitespace',
			'tokenizeTab',
			'tokenizeBlockIndicator',
			'tokenizeText',
		];

		$this->current = 0;

		while ( $this->current < mb_strlen( $line ) )
		{
			$tokenized = false;
			foreach ( $tokenizerMethods as $tokenizerMethod )
			{
				[$consumed, $token] = $this->{$tokenizerMethod}( $line );
				if ( 0 !== $consumed )
				{
					$tokenized     = true;
					$this->current += $consumed;
				}

				if ( $token instanceof Token )
				{
					$this->tokens->add( $token );
				}
			}

			if ( !$tokenized )
			{
				throw new RuntimeException( 'Unknown input: ' . $line[ $this->current ] );
			}
		}
	}

	public function getTokens() : TokenCollection
	{
		return $this->tokens;
	}

	private function tokenizeWhitespace( string $input ) : array
	{
		$pattern = '# #';

		return $this->tokenizePattern( TokenType::Whitespace, $pattern, $input );
	}

	private function tokenizeTab( string $input ) : array
	{
		$pattern = '#\t#';

		return $this->tokenizePattern( TokenType::Tab, $pattern, $input );
	}

	private function tokenizeBlockIndicator( string $input ) : array
	{
		$pattern = '/[>*\-\t#_]/';

		return $this->tokenizePattern( TokenType::BlockElement, $pattern, $input );
	}

	private function tokenizeText( string $input ) : array
	{
		$pattern = '#\w#';

		return $this->tokenizePattern( TokenType::Text, $pattern, $input );
	}

	private function tokenizePattern( string $type, string $pattern, string $input ) : array
	{
		$consumed = 0;
		$char     = $input[ $this->current ] ?? '';

		if ( preg_match( $pattern, $char ) )
		{
			$value = '';
			while ( $char && preg_match( $pattern, $char ) )
			{
				$value .= $char;
				$consumed++;
				$char = $input[ $this->current + $consumed ] ?? false;
			}

			return [$consumed, new Token( $type, $value )];
		}

		return [0, null];
	}
}