<?php
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare( strict_types = 1 );

/**
 *	TestUnit of Strip Filter
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 */

namespace CeusMedia\TemplateEngineTest\Filter;

use CeusMedia\TemplateEngine\Filter\Strip;
use PHPUnit\Framework\TestCase as BaseCase;

/**
 *	TestUnit of Strip Filter
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@coversDefaultClass \CeusMedia\TemplateEngine\Filter\Strip
 */
class StripTest extends BaseCase
{
	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testStripTags(): void
	{
		$filter	= new Strip();
		$actual	= $filter->apply( '<p>Hello <b>World</b></p>', ['tags'] );

		self::assertSame( 'Hello World', $actual );
	}

	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testStripSpace(): void
	{
		$filter	= new Strip();
		$actual	= $filter->apply( '  Hello World  ', ['space'] );

		self::assertSame( 'Hello World', $actual );
	}

	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testStripComments(): void
	{
		$filter	= new Strip();
		$actual	= $filter->apply( 'Hello<!-- comment -->World', ['comments'] );

		self::assertStringNotContainsString( '<!-- comment -->', $actual );
		self::assertStringContainsString( 'Hello', $actual );
	}

	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testStripStyles(): void
	{
		$filter	= new Strip();
		$actual	= $filter->apply( 'Hello<style>.foo{color:red}</style>World', ['styles'] );

		self::assertStringNotContainsString( '<style>', $actual );
		self::assertStringContainsString( 'Hello', $actual );
	}

	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testStripScripts(): void
	{
		$filter	= new Strip();
		$actual	= $filter->apply( 'Hello<script>alert(1)</script>World', ['scripts'] );

		self::assertStringNotContainsString( '<script>', $actual );
		self::assertStringContainsString( 'Hello', $actual );
	}

	/**
	 * Test multiple strip types at once.
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testMultipleTypes(): void
	{
		$filter	= new Strip();
		$actual	= $filter->apply( '  <p>Hello</p>  ', ['space', 'tags'] );

		self::assertSame( 'Hello', $actual );
	}

	/**
	 * Test that unknown arguments are ignored.
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testUnknownArgument(): void
	{
		$filter	= new Strip();
		$actual	= $filter->apply( 'Hello World', ['unknown'] );

		self::assertSame( 'Hello World', $actual );
	}

	/**
	 * Test keywords.
	 * @return void
	 * @covers ::getKeywords
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testKeywords(): void
	{
		$filter	= new Strip();
		self::assertSame( ['strip'], $filter->getKeywords() );
	}
}
