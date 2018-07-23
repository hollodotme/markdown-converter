<?php declare(strict_types=1);

namespace hollodotme\Markdown\Tests\Unit;

use hollodotme\Markdown\BlockElements\BlankLine;
use hollodotme\Markdown\BlockElements\BlockElement;
use hollodotme\Markdown\BlockElements\Code;
use hollodotme\Markdown\BlockElements\Header;
use hollodotme\Markdown\BlockElements\HorizontalRule;
use hollodotme\Markdown\BlockElements\LineBreak;
use hollodotme\Markdown\BlockElements\Quote;
use hollodotme\Markdown\BlockElements\SortedListItem;
use hollodotme\Markdown\BlockElements\Text;
use hollodotme\Markdown\BlockElements\UnsortedListItem;
use hollodotme\Markdown\Interfaces\ParsesMarkdown;
use hollodotme\Markdown\Parser;
use PHPUnit\Framework\TestCase;
use function iterator_to_array;

final class ParserTest extends TestCase
{
	/** @var ParsesMarkdown */
	private $parser;

	protected function setUp() : void
	{
		$this->parser = new Parser();
	}

	protected function tearDown() : void
	{
		$this->parser = null;
	}

	/**
	 * @param string $line
	 * @param string $expectedContents
	 * @param int    $expectedLevel
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @dataProvider headerLineProvider
	 */
	public function testCanGetHeaderElements( string $line, string $expectedContents, int $expectedLevel ) : void
	{
		$elements = $this->parser->getBlockElements( $line );

		/** @var Header $header */
		$header = iterator_to_array( $elements )[0];

		$this->assertSame( BlockElement::HEADER, $header->getName() );
		$this->assertSame( $expectedContents, $header->getContents() );
		$this->assertSame( $expectedLevel, $header->getLevel() );
	}

	public function headerLineProvider() : array
	{
		return [
			[
				'line'             => '# Header Level 1',
				'expectedContents' => 'Header Level 1',
				'expectedLevel'    => 1,
			],
			[
				'line'             => '## Header Level 2',
				'expectedContents' => 'Header Level 2',
				'expectedLevel'    => 2,
			],
			[
				'line'             => '### Header Level 3',
				'expectedContents' => 'Header Level 3',
				'expectedLevel'    => 3,
			],
			[
				'line'             => '#### Header Level 4',
				'expectedContents' => 'Header Level 4',
				'expectedLevel'    => 4,
			],
			[
				'line'             => '##### Header Level 5',
				'expectedContents' => 'Header Level 5',
				'expectedLevel'    => 5,
			],
			[
				'line'             => '###### Header Level 6',
				'expectedContents' => 'Header Level 6',
				'expectedLevel'    => 6,
			],
		];
	}

	/**
	 * @param string $line
	 * @param string $expectedContents
	 * @param int    $expectedIndentLevel
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @dataProvider unsortedListLineProvider
	 */
	public function testCanGetUnsortedListElements(
		string $line,
		string $expectedContents,
		int $expectedIndentLevel
	) : void
	{
		$elements = $this->parser->getBlockElements( $line );

		/** @var UnsortedListItem $listElement */
		$listElement = iterator_to_array( $elements )[0];

		$this->assertSame( BlockElement::UNSORTED_LIST_ITEM, $listElement->getName() );
		$this->assertSame( $expectedContents, $listElement->getContents() );
		$this->assertSame( $expectedIndentLevel, $listElement->getIndentLevel() );
	}

	public function unsortedListLineProvider() : array
	{
		return [
			# "*" as list indicator
			[
				'line'                => '* List Item Level 1',
				'expectedContents'    => 'List Item Level 1',
				'expectedIndentLevel' => 1,
			],
			[
				'line'                => ' * List Item Level 1',
				'expectedContents'    => 'List Item Level 1',
				'expectedIndentLevel' => 1,
			],
			[
				'line'                => '  * List Item Level 2',
				'expectedContents'    => 'List Item Level 2',
				'expectedIndentLevel' => 2,
			],
			[
				'line'                => '   * List Item Level 2',
				'expectedContents'    => 'List Item Level 2',
				'expectedIndentLevel' => 2,
			],
			# "-" as list indicator
			[
				'line'                => '- List Item Level 1',
				'expectedContents'    => 'List Item Level 1',
				'expectedIndentLevel' => 1,
			],
			[
				'line'                => ' - List Item Level 1',
				'expectedContents'    => 'List Item Level 1',
				'expectedIndentLevel' => 1,
			],
			[
				'line'                => '  - List Item Level 2',
				'expectedContents'    => 'List Item Level 2',
				'expectedIndentLevel' => 2,
			],
			[
				'line'                => '   - List Item Level 2',
				'expectedContents'    => 'List Item Level 2',
				'expectedIndentLevel' => 2,
			],
		];
	}

	/**
	 * @param string $line
	 * @param string $expectedContents
	 * @param string $expectedNumbering
	 * @param int    $expectedIndentLevel
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @dataProvider sortedListLineProvider
	 */
	public function testCanGetSortedListElements(
		string $line,
		string $expectedContents,
		string $expectedNumbering,
		int $expectedIndentLevel
	) : void
	{
		$elements = $this->parser->getBlockElements( $line );

		/** @var SortedListItem $listElement */
		$listElement = iterator_to_array( $elements )[0];

		$this->assertSame( BlockElement::SORTED_LIST_ITEM, $listElement->getName() );
		$this->assertSame( $expectedContents, $listElement->getContents() );
		$this->assertSame( $expectedNumbering, $listElement->getNumbering() );
		$this->assertSame( $expectedIndentLevel, $listElement->getIndentLevel() );
	}

	public function sortedListLineProvider() : array
	{
		return [
			[
				'line'                => '1. List Item Level 1',
				'expectedContents'    => 'List Item Level 1',
				'expectedNumbering'   => '1.',
				'expectedIndentLevel' => 1,
			],
			[
				'line'                => '1.1. List Item Level 2',
				'expectedContents'    => 'List Item Level 2',
				'expectedNumbering'   => '1.1.',
				'expectedIndentLevel' => 2,
			],
			[
				'line'                => '1.2.3. List Item Level 3',
				'expectedContents'    => 'List Item Level 3',
				'expectedNumbering'   => '1.2.3.',
				'expectedIndentLevel' => 3,
			],
		];
	}

	/**
	 * @param string $line
	 * @param string $expectedContents
	 * @param int    $expectedIndentLevel
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @dataProvider blockquoteLineProvider
	 */
	public function testCanGetQuoteElements( string $line, string $expectedContents, int $expectedIndentLevel ) : void
	{
		$elements = $this->parser->getBlockElements( $line );

		/** @var Quote $blockquoteElement */
		$blockquoteElement = iterator_to_array( $elements )[0];

		$this->assertSame( BlockElement::QUOTE, $blockquoteElement->getName() );
		$this->assertSame( $expectedContents, $blockquoteElement->getContents() );
		$this->assertSame( $expectedIndentLevel, $blockquoteElement->getIndentLevel() );
	}

	public function blockquoteLineProvider() : array
	{
		return [
			[
				'line'                => '> Blockquote Level 1',
				'expectedContents'    => 'Blockquote Level 1',
				'expectedIndentLevel' => 1,
			],
			[
				'line'                => ' > Blockquote Level 1',
				'expectedContents'    => 'Blockquote Level 1',
				'expectedIndentLevel' => 1,
			],
			[
				'line'                => '  > Blockquote Level 2',
				'expectedContents'    => 'Blockquote Level 2',
				'expectedIndentLevel' => 2,
			],
			[
				'line'                => '   > Blockquote Level 2',
				'expectedContents'    => 'Blockquote Level 2',
				'expectedIndentLevel' => 2,
			],
		];
	}

	/**
	 * @param string $line
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider horizontalRuleLineProvider
	 */
	public function testCanGetHorizontalRule( string $line ) : void
	{
		$elements = $this->parser->getBlockElements( $line );

		$hr = iterator_to_array( $elements )[0];

		$this->assertInstanceOf( HorizontalRule::class, $hr );
		$this->assertSame( BlockElement::HORIZONTAL_RULE, $hr->getName() );
	}

	public function horizontalRuleLineProvider() : array
	{
		return [
			[
				'line' => '---',
			],
			[
				'line' => '- - -',
			],
			[
				'line' => '***',
			],
			[
				'line' => '* * *',
			],
			[
				'line' => '___',
			],
			[
				'line' => '_ _ _',
			],
		];
	}

	/**
	 * @param string $line
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider lineBreakLineProvider
	 */
	public function testCanGetLineBreak( string $line ) : void
	{
		$elements = $this->parser->getBlockElements( $line );

		$lineBreak = iterator_to_array( $elements )[0];

		$this->assertInstanceOf( LineBreak::class, $lineBreak );
		$this->assertSame( BlockElement::LINE_BREAK, $lineBreak->getName() );
	}

	public function lineBreakLineProvider() : array
	{
		return [
			[
				'line' => 'Something   ',
			],
			[
				'line' => ' Something  ',
			],
			[
				'line' => 'Something -  ',
			],
		];
	}

	/**
	 * @param string $line
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider blankLineProvider
	 */
	public function testCanGetBlankLine( string $line ) : void
	{
		$elements = $this->parser->getBlockElements( $line );

		$blankLine = iterator_to_array( $elements )[0];

		$this->assertInstanceOf( BlankLine::class, $blankLine );
		$this->assertSame( BlockElement::BLANK_LINE, $blankLine->getName() );
	}

	public function blankLineProvider() : array
	{
		return [
			[
				'line' => '',
			],
			[
				'line' => '  ',
			],
			[
				'line' => "\t",
			],
		];
	}

	/**
	 * @param string $line
	 * @param string $expectedContents
	 * @param int    $expectedIndentLevel
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @dataProvider codeLineProvider
	 */
	public function testCanGetCode( string $line, string $expectedContents, int $expectedIndentLevel ) : void
	{
		$elements = $this->parser->getBlockElements( $line );

		/** @var Code $code */
		$code = iterator_to_array( $elements )[0];

		$this->assertSame( BlockElement::CODE, $code->getName() );
		$this->assertSame( $expectedContents, $code->getContents() );
		$this->assertSame( $expectedIndentLevel, $code->getIndentLevel() );
	}

	public function codeLineProvider() : array
	{
		return [
			[
				'line'                => '    { Code; }',
				'expectedContents'    => '{ Code; }',
				'expectedIndentLevel' => 1,
			],
			[
				'line'                => "\t{ Code; }",
				'expectedContents'    => '{ Code; }',
				'expectedIndentLevel' => 1,
			],
			[
				'line'                => '        { Code; }',
				'expectedContents'    => '{ Code; }',
				'expectedIndentLevel' => 2,
			],
			[
				'line'                => "\t\t{ Code; }",
				'expectedContents'    => '{ Code; }',
				'expectedIndentLevel' => 2,
			],
		];
	}

	/**
	 * @param string $line
	 * @param string $expectedContents
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider textLineProvider
	 */
	public function testCanGetTextElement( string $line, string $expectedContents ) : void
	{
		$elements = $this->parser->getBlockElements( $line );

		/** @var Text $text */
		$text = iterator_to_array( $elements )[0];

		$this->assertSame( BlockElement::TEXT, $text->getName() );
		$this->assertSame( $expectedContents, $text->getContents() );
	}

	public function textLineProvider() : array
	{
		return [
			[
				'line'             => 'Some text',
				'expectedContents' => 'Some text',
			],
			[
				'line'             => ' Some text with leading whitespace',
				'expectedContents' => 'Some text with leading whitespace',
			],
			[
				'line'             => ' Some text with leading and trailing whitespace ',
				'expectedContents' => 'Some text with leading and trailing whitespace',
			],
			[
				'line'             => '-- Some text with leading dashes',
				'expectedContents' => '-- Some text with leading dashes',
			],
			[
				'line'             => '[A link](https://example.com)',
				'expectedContents' => '[A link](https://example.com)',
			],
		];
	}
}
