<?php
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare( strict_types = 1 );

/**
 *	TestUnit of File Plugin
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 */

namespace CeusMedia\TemplateEngineTest\Plugin;

use CeusMedia\TemplateEngine\Plugin\File;
use PHPUnit\Framework\TestCase as BaseCase;

/**
 *	TestUnit of File Plugin
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@coversDefaultClass \CeusMedia\TemplateEngine\Plugin\File
 */
class FileTest extends BaseCase
{
	/**
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\File
	 */
	public function testWork(): void
	{
		$path		= dirname( __FILE__ ).'/../assets/';
		$template	= 'Before <%file(file_plugin_test.txt)%> After';
		$elements	= [];
		$plugin	= new File( ['path' => $path] );
		$actual	= $plugin->work( $template, $elements );

		// The file tag should be replaced with a hash placeholder
		self::assertStringNotContainsString( '<%file(', $actual );
		self::assertStringContainsString( 'Before', $actual );
		self::assertStringContainsString( 'After', $actual );
		// The content should be stored in elements
		$found	= FALSE;
		foreach( $elements as $key => $value ){
			if( strpos( $key, 'STE' ) === 0 && strpos( $value, 'This is a test file' ) !== FALSE ){
				$found	= TRUE;
				break;
			}
		}
		self::assertTrue( $found, 'File content should be stored in elements' );
	}

	/**
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\File
	 */
	public function testWorkWithSubdirectory(): void
	{
		$path		= dirname( __FILE__ ).'/../assets/';
		$template	= '<%file(subdir/nested.txt)%>';
		$elements	= [];
		$plugin	= new File( ['path' => $path] );
		$actual	= $plugin->work( $template, $elements );

		self::assertStringNotContainsString( '<%file(', $actual );
		$found	= FALSE;
		foreach( $elements as $key => $value ){
			if( strpos( $key, 'STE' ) === 0 && strpos( $value, 'This file is in a subdirectory' ) !== FALSE ){
				$found	= TRUE;
				break;
			}
		}
		self::assertTrue( $found, 'Nested file content should be stored in elements' );
	}

	/**
	 * Test that path traversal attempts are blocked.
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\File
	 */
	public function testPathTraversalPrevention(): void
	{
		$path		= dirname( __FILE__ ).'/../assets/';
		$template	= '<%file(../../../etc/passwd)%>';
		$elements	= [];
		$plugin	= new File( ['path' => $path, 'mode' => 'silent'] );
		$actual	= $plugin->work( $template, $elements );

		// In silent mode, the traversal attempt should result in empty replacement
		self::assertStringNotContainsString( 'root:', $actual );
		self::assertStringNotContainsString( '<%file(', $actual );
	}

	/**
	 * Test that path traversal in strict mode throws an exception.
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\File
	 */
	public function testPathTraversalStrictMode(): void
	{
		$path		= dirname( __FILE__ ).'/../assets/';
		$template	= '<%file(../../../etc/passwd)%>';
		$elements	= [];
		$plugin	= new File( ['path' => $path, 'mode' => 'strict'] );

		// In strict mode, exceptions are thrown
		// The traversal attempt will cause an InvalidArgumentException which is caught
		// and re-thrown as IoException in strict mode
		$this->expectException( \CeusMedia\Common\Exception\IO::class );
		$plugin->work( $template, $elements );
	}

	/**
	 * Test that path traversal in verbose mode shows error message.
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\File
	 */
	public function testPathTraversalVerboseMode(): void
	{
		$path		= dirname( __FILE__ ).'/../assets/';
		$template	= '<%file(../../../etc/passwd)%>';
		$elements	= [];
		$plugin	= new File( ['path' => $path, 'mode' => 'verbose'] );
		$actual	= $plugin->work( $template, $elements );

		// In verbose mode, the error message should be shown
		self::assertStringContainsString( 'Missing file:', $actual );
		self::assertStringNotContainsString( 'root:', $actual );
	}

	/**
	 * Test that non-existent files are handled gracefully.
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\File
	 */
	public function testNonExistentFile(): void
	{
		$path		= dirname( __FILE__ ).'/../assets/';
		$template	= '<%file(nonexistent.txt)%>';
		$elements	= [];
		$plugin	= new File( ['path' => $path, 'mode' => 'silent'] );
		$actual	= $plugin->work( $template, $elements );

		self::assertStringNotContainsString( '<%file(', $actual );
	}

	/**
	 * Test that template tags in file content are escaped.
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\File
	 */
	public function testTagEscaping(): void
	{
		$path		= dirname( __FILE__ ).'/../assets/';
		$template	= '<%file(file_plugin_test.txt)%>';
		$elements	= [];
		$plugin	= new File( ['path' => $path] );
		$plugin->work( $template, $elements );

		// Check that any <% %> tags in the loaded content are escaped
		foreach( $elements as $value ){
			self::assertStringNotContainsString( '<%', $value );
		}
	}
}
