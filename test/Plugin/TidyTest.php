<?php
declare(strict_types=1);

/**
 *	TestUnit of Tidy plugin.
 *
 *	Copyright (c) 2011-2024 Christian Würker (ceusmedia.de)
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine_Plugin
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2024 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
namespace CeusMedia\TemplateEngine\test\Plugin;

use CeusMedia\TemplateEngine\Plugin\Tidy;
use PHPUnit\Framework\TestCase;

class TidyTest extends TestCase
{
	/**
	 *	Skip all tests if tidy extension is not loaded.
	 */
	public function setUp(): void
	{
		if( !extension_loaded( 'tidy' ) )
			self::markTestSkipped( 'Tidy extension is not loaded' );
	}

	/**
	 *	Test basic HTML cleanup.
	 *	@covers		CeusMedia\TemplateEngine\Plugin\Tidy::work
	 */
	public function testWork(): void
	{
		$plugin	= new Tidy();
		$elements	= [];
		$actual	= $plugin->work( '<div><p>Test</p></div>', $elements );
		self::assertIsString( $actual );
		self::assertStringContainsString( '<p>', $actual );
	}

	/**
	 *	Test that the plugin returns a string.
	 *	@covers		CeusMedia\TemplateEngine\Plugin\Tidy::work
	 */
	public function testWork_ReturnType(): void
	{
		$plugin	= new Tidy();
		$elements	= [];
		$actual	= $plugin->work( '<html><body><p>Content</p></body></html>', $elements );
		self::assertIsString( $actual );
	}

	/**
	 *	Test keyword.
	 *	@covers		CeusMedia\TemplateEngine\Plugin\Tidy::getKeyword
	 */
	public function testGetKeyword(): void
	{
		$plugin	= new Tidy();
		self::assertEquals( 'tidy', $plugin->getKeyword() );
	}

	/**
	 *	Test plugin type is post.
	 *	@covers		CeusMedia\TemplateEngine\Plugin\Tidy::getType
	 */
	public function testGetType(): void
	{
		$plugin	= new Tidy();
		self::assertEquals( 'post', $plugin->getType() );
	}

	/**
	 *	Test plugin priority.
	 *	@covers		CeusMedia\TemplateEngine\Plugin\Tidy::getPriority
	 */
	public function testGetPriority(): void
	{
		$plugin	= new Tidy();
		self::assertEquals( 9, $plugin->getPriority() );
	}

	/**
	 *	Test with empty string.
	 *	@covers		CeusMedia\TemplateEngine\Plugin\Tidy::work
	 */
	public function testWork_EmptyString(): void
	{
		$plugin	= new Tidy();
		$elements	= [];
		$actual	= $plugin->work( '', $elements );
		self::assertIsString( $actual );
	}
}
