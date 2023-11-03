<?php
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare( strict_types = 1 );

/**
 *	TestUnit of Template
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 */

namespace CeusMedia\TemplateEngineTest\Filter;

use CeusMedia\TemplateEngine\Filter\Code;
use PHPUnit\Framework\TestCase as BaseCase;

/**
 *	TestUnit of Template
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@coversDefaultClass \CeusMedia\TemplateEngine\Filter\Code
 */
class CodeTest extends BaseCase
{
	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testApply_Code(): void
	{
		$filter	= new Code();
		$actual	= $filter->apply( 'Code' );
		self::assertEquals( '<code>Code</code>', $actual );
	}

	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testApply_Json(): void
	{
		$filter	= new Code();
		$expect	= json_encode( 'Code', JSON_PRETTY_PRINT );
		$actual	= $filter->apply( '"Code"', ['json'] );
		self::assertEquals( $expect, $actual );
	}

	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testApply_Highlight(): void
	{
		$filter	= new Code();
		$actual	= $filter->apply( 'Code', ['highlight'] );
		self::assertEquals( highlight_string( 'Code', TRUE ), $actual );
	}

	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testApply_XMP(): void
	{
		$filter	= new Code();
		$actual	= $filter->apply( 'Code', ['xmp'] );
		self::assertEquals( '<xmp>Code</xmp>', $actual );
	}

	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testApply_XMP_SQL(): void
	{
		$filter	= new Code();
		$actual	= $filter->apply( 'Code', ['xmp', 'sql'] );
		self::assertEquals( '<xmp class="sql">Code</xmp>', $actual );
	}
}