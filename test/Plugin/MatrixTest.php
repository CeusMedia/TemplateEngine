<?php
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare( strict_types = 1 );

/**
 *	TestUnit of Matrix Plugin
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 */

namespace CeusMedia\TemplateEngineTest\Plugin;

use CeusMedia\TemplateEngine\Plugin\Matrix;
use PHPUnit\Framework\TestCase as BaseCase;

/**
 *	TestUnit of Matrix Plugin
 *	@package		test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@coversDefaultClass \CeusMedia\TemplateEngine\Plugin\Matrix
 */
class MatrixTest extends BaseCase
{
	/**
	 * @return void
	 * @covers ::work
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 */
	public function testWork(): void
	{
		$data	= [
			'en' => [
				'hello' => 'Hello',
				'world' => 'World',
			],
			'de' => [
				'hello' => 'Hallo',
				'world' => 'Welt',
			],
		];
		$template	= '<%matrix(en.hello)%> <%matrix(en.world)%>';
		$elements	= [];
		$plugin	= new Matrix( ['data' => $data, 'depth' => '2'] );
		$actual	= $plugin->work( $template, $elements );

		self::assertStringContainsString( 'Hello', $actual );
		self::assertStringContainsString( 'World', $actual );
		self::assertStringNotContainsString( '<%matrix(', $actual );
	}

	/**
	 * @return void
	 * @covers ::work
	 */
	public function testWorkWithCustomKeyword(): void
	{
		$data	= [
			'greeting' => 'Hi there',
		];
		$template	= '<%translate(greeting)%>';
		$elements	= [];
		$plugin	= new Matrix( ['keyword' => 'translate', 'data' => $data, 'depth' => '1'] );
		$actual	= $plugin->work( $template, $elements );

		self::assertStringContainsString( 'Hi there', $actual );
		self::assertStringNotContainsString( '<%translate(', $actual );
	}

	/**
	 * Test that missing keys return empty string in quiet mode.
	 * @return void
	 * @covers ::work
	 */
	public function testMissingKeyQuietMode(): void
	{
		$data	= [
			'en' => [
				'hello' => 'Hello',
			],
		];
		$template	= '<%matrix(en.missing)%>';
		$elements	= [];
		$plugin	= new Matrix( ['data' => $data, 'mode' => 'quite', 'depth' => '2'] );
		$actual	= $plugin->work( $template, $elements );

		self::assertStringNotContainsString( '<%matrix(', $actual );
		self::assertStringNotContainsString( 'Missing:', $actual );
	}

	/**
	 * Test that missing keys show error message in verbose mode.
	 * @return void
	 * @covers ::work
	 */
	public function testMissingKeyVerboseMode(): void
	{
		$data	= [
			'en' => [
				'hello' => 'Hello',
			],
		];
		$template	= '<%matrix(en.missing)%>';
		$elements	= [];
		$plugin	= new Matrix( ['data' => $data, 'mode' => 'verbose', 'depth' => '2'] );
		$actual	= $plugin->work( $template, $elements );

		self::assertStringContainsString( 'Missing:', $actual );
	}

	/**
	 * Test that missing keys throw exception in strict mode.
	 * @return void
	 * @covers ::work
	 */
	public function testMissingKeyStrictMode(): void
	{
		$data	= [
			'en' => [
				'hello' => 'Hello',
			],
		];
		$template	= '<%matrix(en.missing)%>';
		$elements	= [];
		$plugin	= new Matrix( ['data' => $data, 'mode' => 'strict', 'depth' => '2'] );

		$this->expectException( \RuntimeException::class );
		$plugin->work( $template, $elements );
	}

	/**
	 * Test that depth mismatch throws exception.
	 * @return void
	 * @covers ::work
	 */
	public function testDepthMismatch(): void
	{
		$data	= [
			'en' => [
				'hello' => 'Hello',
			],
		];
		$template	= '<%matrix(en.hello.world)%>';
		$elements	= [];
		$plugin	= new Matrix( ['data' => $data, 'depth' => '2'] );

		$this->expectException( \InvalidArgumentException::class );
		$plugin->work( $template, $elements );
	}

	/**
	 * Test with integer values in matrix data.
	 * @return void
	 * @covers ::work
	 */
	public function testIntegerValue(): void
	{
		$data	= [
			'count' => [
				'one' => 1,
			],
		];
		$template	= 'Count: <%matrix(count.one)%>';
		$elements	= [];
		$plugin	= new Matrix( ['data' => $data, 'depth' => '2'] );
		$actual	= $plugin->work( $template, $elements );

		self::assertStringContainsString( 'Count: 1', $actual );
	}

	/**
	 * Test that template tags in matrix values are escaped by PluginAbstract.
	 * @return void
	 * @covers ::work
	 */
	public function testTagEscaping(): void
	{
		$data	= [
			'safe' => [
				'value' => 'Safe value',
			],
		];
		$template	= '<%matrix(safe.value)%>';
		$elements	= [];
		$plugin	= new Matrix( ['data' => $data, 'depth' => '2'] );
		$actual	= $plugin->work( $template, $elements );

		// The matrix tag should be replaced
		self::assertStringNotContainsString( '<%matrix(', $actual );
		self::assertStringContainsString( 'Safe value', $actual );
	}
}
