<?php
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare( strict_types = 1 );

/**
 *	TestUnit of Template
 *	@package		test
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 */

namespace CeusMedia\TemplateEngineTest\Filter;

use CeusMedia\TemplateEngine\Filter\Rot13;
use PHPUnit\Framework\TestCase as BaseCase;

/**
 *	TestUnit of Template
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@coversDefaultClass \CeusMedia\TemplateEngine\Filter\Rot13
 */
class Rot13Test extends BaseCase
{
	/**
	 * @return void
	 * @covers ::apply
	 * @covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testApply(): void
	{
		$filter	= new Rot13();
		$actual	= $filter->apply( 'Code' );
		self::assertEquals( str_rot13( 'Code' ), $actual );
	}
}