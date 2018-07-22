<?php declare(strict_types=1);

namespace hollodotme\Markdown\Tests\Unit;

use hollodotme\Markdown\Elements\BlankLine;
use hollodotme\Markdown\Elements\Blockquote;
use hollodotme\Markdown\Elements\Element;
use hollodotme\Markdown\Elements\Header;
use hollodotme\Markdown\Elements\HorizontalRule;
use hollodotme\Markdown\Elements\LineBreak;
use hollodotme\Markdown\Elements\SortedListItem;
use hollodotme\Markdown\Elements\UnsortedListItem;
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
	 * @param Header $expectedHeader
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider headerLineProvider
	 */
	public function testCanGetHeaderElements( string $line, Header $expectedHeader ) : void
	{
		$elements = $this->parser->getElements( $line );

		/** @var Header $header */
		$header = iterator_to_array( $elements )[0];

		$this->assertEquals( $expectedHeader, $header );
		$this->assertSame( $expectedHeader->getName(), $header->getName() );
		$this->assertSame( $expectedHeader->getContents(), $header->getContents() );
		$this->assertSame( $expectedHeader->getLevel(), $header->getLevel() );
	}

	public function headerLineProvider() : array
	{
		return [
			[
				'line'           => '# Header Level 1',
				'expectedHeader' => new Header( 'Header Level 1', 1 ),
			],
			[
				'line'           => '## Header Level 2',
				'expectedHeader' => new Header( 'Header Level 2', 2 ),
			],
			[
				'line'           => '### Header Level 3',
				'expectedHeader' => new Header( 'Header Level 3', 3 ),
			],
			[
				'line'           => '#### Header Level 4',
				'expectedHeader' => new Header( 'Header Level 4', 4 ),
			],
			[
				'line'           => '##### Header Level 5',
				'expectedHeader' => new Header( 'Header Level 5', 5 ),
			],
			[
				'line'           => '###### Header Level 6',
				'expectedHeader' => new Header( 'Header Level 6', 6 ),
			],
			[
				'line'           => '#### Header # Level 4',
				'expectedHeader' => new Header( 'Header # Level 4', 4 ),
			],
			[
				'line'           => '### # Header Level 3',
				'expectedHeader' => new Header( '# Header Level 3', 3 ),
			],
		];
	}

	/**
	 * @param string           $line
	 * @param UnsortedListItem $expectedListElement
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider unsortedListLineProvider
	 */
	public function testCanGetUnsortedListElements( string $line, UnsortedListItem $expectedListElement ) : void
	{
		$elements = $this->parser->getElements( $line );

		/** @var UnsortedListItem $listElement */
		$listElement = iterator_to_array( $elements )[0];

		$this->assertEquals( $expectedListElement, $listElement );
		$this->assertSame( $expectedListElement->getName(), $listElement->getName() );
		$this->assertSame( $expectedListElement->getContents(), $listElement->getContents() );
		$this->assertSame( $expectedListElement->getIndentLevel(), $listElement->getIndentLevel() );
	}

	public function unsortedListLineProvider() : array
	{
		return [
			# "*" as list indicator
			[
				'line'                => '* List Item Level 1',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 1', 1 ),
			],
			[
				'line'                => ' * List Item Level 1',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 1', 1 ),
			],
			[
				'line'                => '  * List Item Level 2',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 2', 2 ),
			],
			[
				'line'                => '   * List Item Level 2',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 2', 2 ),
			],
			[
				'line'                => '    * List Item Level 3',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 3', 3 ),
			],
			[
				'line'                => '     * List Item Level 3',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 3', 3 ),
			],
			[
				'line'                => ' * List * Item Level 1 ',
				'expectedListElement' => new UnsortedListItem( 'List * Item Level 1', 1 ),
			],
			# "-" as list indicator
			[
				'line'                => '- List Item Level 1',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 1', 1 ),
			],
			[
				'line'                => ' - List Item Level 1',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 1', 1 ),
			],
			[
				'line'                => '  - List Item Level 2',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 2', 2 ),
			],
			[
				'line'                => '   - List Item Level 2',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 2', 2 ),
			],
			[
				'line'                => '    - List Item Level 3',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 3', 3 ),
			],
			[
				'line'                => '     - List Item Level 3',
				'expectedListElement' => new UnsortedListItem( 'List Item Level 3', 3 ),
			],
			[
				'line'                => ' - List - Item Level 1 ',
				'expectedListElement' => new UnsortedListItem( 'List - Item Level 1', 1 ),
			],
		];
	}

	/**
	 * @param string         $line
	 * @param SortedListItem $expectedListElement
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider sortedListLineProvider
	 */
	public function testCanGetSortedListElements( string $line, SortedListItem $expectedListElement ) : void
	{
		$elements = $this->parser->getElements( $line );

		/** @var SortedListItem $listElement */
		$listElement = iterator_to_array( $elements )[0];

		$this->assertEquals( $expectedListElement, $listElement );
		$this->assertSame( $expectedListElement->getName(), $listElement->getName() );
		$this->assertSame( $expectedListElement->getContents(), $listElement->getContents() );
		$this->assertSame( $expectedListElement->getNumbering(), $listElement->getNumbering() );
		$this->assertSame( $expectedListElement->getIndentLevel(), $listElement->getIndentLevel() );
	}

	public function sortedListLineProvider() : array
	{
		return [
			[
				'line'                => '1. List Item Level 1',
				'expectedListElement' => new SortedListItem( 'List Item Level 1', '1.' ),
			],
			[
				'line'                => ' 1. List Item Level 1 ',
				'expectedListElement' => new SortedListItem( 'List Item Level 1', '1.' ),
			],
			[
				'line'                => '1.1. List Item Level 2',
				'expectedListElement' => new SortedListItem( 'List Item Level 2', '1.1.' ),
			],
			[
				'line'                => ' 1.2. List Item Level 2',
				'expectedListElement' => new SortedListItem( 'List Item Level 2', '1.2.' ),
			],
			[
				'line'                => '1.2.3. List Item Level 3',
				'expectedListElement' => new SortedListItem( 'List Item Level 3', '1.2.3.' ),
			],
		];
	}

	/**
	 * @param string     $line
	 * @param Blockquote $expectedBlockquoteElement
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider blockquoteLineProvider
	 */
	public function testCanGetBlockquoteElements( string $line, Blockquote $expectedBlockquoteElement ) : void
	{
		$elements = $this->parser->getElements( $line );

		/** @var Blockquote $blockquoteElement */
		$blockquoteElement = iterator_to_array( $elements )[0];

		$this->assertEquals( $expectedBlockquoteElement, $blockquoteElement );
		$this->assertSame( $expectedBlockquoteElement->getName(), $blockquoteElement->getName() );
		$this->assertSame( $expectedBlockquoteElement->getContents(), $blockquoteElement->getContents() );
		$this->assertSame( $expectedBlockquoteElement->getIndentLevel(), $blockquoteElement->getIndentLevel() );
	}

	public function blockquoteLineProvider() : array
	{
		return [
			[
				'line'                      => '> Blockquote Level 1',
				'expectedBlockquoteElement' => new Blockquote( 'Blockquote Level 1', 1 ),
			],
			[
				'line'                      => ' > Blockquote Level 1 ',
				'expectedBlockquoteElement' => new Blockquote( 'Blockquote Level 1', 1 ),
			],
			[
				'line'                      => '  > Blockquote Level 2',
				'expectedBlockquoteElement' => new Blockquote( 'Blockquote Level 2', 2 ),
			],
			[
				'line'                      => '   > Blockquote Level 2 ',
				'expectedBlockquoteElement' => new Blockquote( 'Blockquote Level 2', 2 ),
			],
			[
				'line'                      => '> >Blockquote Level 1 ',
				'expectedBlockquoteElement' => new Blockquote( '>Blockquote Level 1', 1 ),
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
		$elements = $this->parser->getElements( $line );

		$hr = iterator_to_array( $elements )[0];

		$this->assertInstanceOf( HorizontalRule::class, $hr );
		$this->assertSame( Element::HORIZONTAL_RULE, $hr->getName() );
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
		$elements = $this->parser->getElements( $line );

		$lineBreak = iterator_to_array( $elements )[0];

		$this->assertInstanceOf( LineBreak::class, $lineBreak );
		$this->assertSame( Element::LINE_BREAK, $lineBreak->getName() );
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
		$elements = $this->parser->getElements( $line );

		$blankLine = iterator_to_array( $elements )[0];

		$this->assertInstanceOf( BlankLine::class, $blankLine );
		$this->assertSame( Element::BLANK_LINE, $blankLine->getName() );
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
}
