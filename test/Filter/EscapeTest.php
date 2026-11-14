<?php
declare(strict_types=1);

/**
 *	TestUnit of Escape filter.
 *
 *	Copyright (c) 2011-2026 Christian Würker (ceusmedia.de)
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
 *	@package		CeusMedia_TemplateEngine_Filter
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2026 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
namespace CeusMedia\TemplateEngineTest\Filter;

use CeusMedia\TemplateEngine\Filter\Escape;
use PHPUnit\Framework\TestCase;

class EscapeTest extends TestCase
{
	/**
	 *	Test basic HTML entity escaping.
	 *	@covers		CeusMedia\TemplateEngine\Filter\Escape::apply
	 */
	public function testApply(): void
	{
		$filter	= new Escape();
		$actual	= $filter->apply( '<div class="test">&nbsp;</div>' );
		self::assertEquals( '&lt;div class=&quot;test&quot;&gt;&amp;nbsp;&lt;/div&gt;', $actual );
	}

	/**
	 *	Test that single quotes are escaped.
	 *	@covers		CeusMedia\TemplateEngine\Filter\Escape::apply
	 */
	public function testSingleQuotes(): void
	{
		$filter	= new Escape();
		$actual	= $filter->apply( "it's a test" );
		self::assertEquals( 'it&#039;s a test', $actual );
	}

	/**
	 *	Test XSS prevention with script tags.
	 *	@covers		CeusMedia\TemplateEngine\Filter\Escape::apply
	 */
	public function testXssPrevention(): void
	{
		$filter	= new Escape();
		$actual	= $filter->apply( '<script>alert("xss")</script>' );
		self::assertStringNotContainsString( '<script>', $actual );
		self::assertStringContainsString( '&lt;script&gt;', $actual );
	}

	/**
	 *	Test with custom encoding.
	 *	@covers		CeusMedia\TemplateEngine\Filter\Escape::apply
	 */
	public function testCustomEncoding(): void
	{
		$filter	= new Escape();
		$actual	= $filter->apply( '<>&', ['ISO-8859-1'] );
		self::assertEquals( '&lt;&gt;&amp;', $actual );
	}

	/**
	 *	Test double encode disabled.
	 *	@covers		CeusMedia\TemplateEngine\Filter\Escape::apply
	 */
	public function testDoubleEncodeDisabled(): void
	{
		$filter	= new Escape();
		$actual	= $filter->apply( '&amp;entity;', ['UTF-8', FALSE] );
		self::assertEquals( '&amp;entity;', $actual );
	}

	/**
	 *	Test double encode enabled (default).
	 *	@covers		CeusMedia\TemplateEngine\Filter\Escape::apply
	 */
	public function testDoubleEncodeEnabled(): void
	{
		$filter	= new Escape();
		$actual	= $filter->apply( '&amp;entity;' );
		self::assertEquals( '&amp;amp;entity;', $actual );
	}

	/**
	 *	Test keywords.
	 *	@covers		CeusMedia\TemplateEngine\Filter\Escape::getKeywords
	 */
	public function testKeywords(): void
	{
		$filter	= new Escape();
		self::assertEquals( ['escape', 'htmlentities'], $filter->getKeywords() );
	}

	/**
	 *	Test empty string.
	 *	@covers		CeusMedia\TemplateEngine\Filter\Escape::apply
	 */
	public function testEmptyString(): void
	{
		$filter	= new Escape();
		$actual	= $filter->apply( '' );
		self::assertEquals( '', $actual );
	}
}
