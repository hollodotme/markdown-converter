<?php declare(strict_types=1);

use hollodotme\Markdown\Tokenizer;

require_once __DIR__ . '/../../vendor/autoload.php';

$tokenizer = new Tokenizer();
$line      = [];
$lines[]   = '> some blockquote';
$lines[]   = '* A list element';
$lines[]   = '- A list element';
$lines[]   = '___';

foreach ( $lines as $line )
{
	$tokenizer->tokenizeLine( $line );
}

var_dump( $tokenizer->getTokens() );