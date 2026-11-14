<?php
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare( strict_types = 1 );

/**
 *	TestUnit of Inclusion Plugin
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 */

namespace CeusMedia\TemplateEngineTest\Plugin;

use CeusMedia\TemplateEngine\Plugin\Inclusion;
use CeusMedia\TemplateEngine\Template;
use PHPUnit\Framework\TestCase as BaseCase;

/**
 *	TestUnit of Inclusion Plugin
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@coversDefaultClass \CeusMedia\TemplateEngine\Plugin\Inclusion
 */
class InclusionTest extends BaseCase
{
	protected function setUp(): void
	{
		parent::setUp();
		Template::setTemplatePath( dirname( __FILE__ ).'/../assets/' );
	}

	protected function tearDown(): void
	{
		Template::clear();
		parent::tearDown();
	}

	/**
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testWork(): void
	{
		// Create a simple included template
		$includedPath	= dirname( __FILE__ ).'/../assets/included_test.tmpl';
		file_put_contents( $includedPath, 'Included content: <%name%>' );

		$template	= 'Before <%include(included_test.tmpl)%> After';
		$elements	= ['name' => 'World'];
		$plugin	= new Inclusion();
		$actual	= $plugin->work( $template, $elements );

		// The include tag should be replaced with a hash placeholder
		self::assertStringNotContainsString( '<%include(', $actual );
		self::assertStringContainsString( 'Before', $actual );
		self::assertStringContainsString( 'After', $actual );

		// The content should be stored in elements
		$found	= FALSE;
		foreach( $elements as $key => $value ){
			if( strpos( $key, 'STE' ) === 0 && strpos( $value, 'Included content:' ) !== FALSE ){
				$found	= TRUE;
				break;
			}
		}
		self::assertTrue( $found, 'Included content should be stored in elements' );

		// Cleanup
		unlink( $includedPath );
	}

	/**
	 * Test that no include tags returns template unchanged.
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testNoIncludeTags(): void
	{
		$template	= 'No includes here';
		$elements	= [];
		$plugin	= new Inclusion();
		$actual	= $plugin->work( $template, $elements );

		self::assertSame( 'No includes here', $actual );
	}

	/**
	 * Test with custom keyword.
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testCustomKeyword(): void
	{
		$includedPath	= dirname( __FILE__ ).'/../assets/partial_test.tmpl';
		file_put_contents( $includedPath, 'Partial' );

		$template	= '<%embed(partial_test.tmpl)%>';
		$elements	= [];
		$plugin	= new Inclusion();
		// Can't set keyword directly, but we can verify the pattern works
		$actual	= $plugin->work( $template, $elements );

		// Since keyword is 'include' by default, 'embed' won't match
		self::assertStringContainsString( '<%embed(', $actual );

		unlink( $includedPath );
	}
}
