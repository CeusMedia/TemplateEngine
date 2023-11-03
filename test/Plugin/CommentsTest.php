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

namespace CeusMedia\TemplateEngineTest\Plugin;

use CeusMedia\TemplateEngine\Plugin\Comments;
use PHPUnit\Framework\TestCase as BaseCase;

/**
 *	TestUnit of Template
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@coversDefaultClass \CeusMedia\TemplateEngine\Plugin\Comments
 */
class CommentsTest extends BaseCase
{
	/**
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 */
	public function testWork(): void
	{
		$template	= '1 <%--2--%> 3<!--4-->';
		$elements	= [];
		$plugin	= new Comments();
		$actual	= $plugin->work( $template, $elements );
		self::assertEquals( '1  3<!--4-->', $actual );
	}

	/**
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 */
	public function testWorkWithRemove(): void
	{
		$template	= '1 <%--2--%> 3<!--4-->';
		$elements	= [];
		$plugin	= new Comments( ['remove' => TRUE] );
		$actual	= $plugin->work( $template, $elements );
		self::assertEquals( '1  3', $actual );
	}
}