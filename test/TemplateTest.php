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

namespace CeusMedia\TemplateEngineTest;

use CeusMedia\Common\ADT\Collection\Dictionary;
use CeusMedia\TemplateEngine\Plugin\Comments;
use CeusMedia\TemplateEngine\Template;
use PHPUnit\Framework\TestCase as BaseCase;

use ArrayObject;
use ReflectionException;
use ReflectionObject;

/**
 *	TestUnit of Template
 *	@package		test
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@coversDefaultClass \CeusMedia\TemplateEngine\Template
 */
class TemplateTest extends BaseCase
{
	private Template $template;

	protected array $mockElements;
	protected string $path;

	public function setUp(): void
	{
		Template::clear();
		Template::setTemplatePath( '' );
		$this->path			= dirname( __FILE__ )."/assets/";
		$this->template		= new Template( $this->path.'template_testcase1.html' );
		$this->mockElements	= array(
			'user'	=> "Welt",
			'list'	=> array(
				6, 5, 4
			),
			'map1'	=> array(
				'string1'	=> 'value1',
				'list1'	=> array(
					1, 2, 3
				),
				'map1'		=> array(
					'string1'	=> 'value2',
					'float1'		=> M_PI,
					'list1'	=> array(
						1, 2, 3
					),
				),
				'map2'		=> array(
					'string1'	=> 'value2',
					'float1'		=> M_PI,
					'list1'	=> array(
						1, 2, 3
					),
				),
			)
		);

	}

	/**
	 * @return void
	 * @covers ::getElements
	 * @covers \CeusMedia\TemplateEngine\Template
	 * /	 */
	public function testInitiallyNoElements(): void
	{
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 0, $size );
	}

	/**
	 * @return void
	 * @covers ::add
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAdd1(): void
	{
		$expected	= 18;
		$actual	= $this->template->add( $this->mockElements );
		self::assertEquals( $expected, $actual );
	}

	/**
	 * @return void
	 * @covers ::add
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAdd2(): void
	{
		$tags	= array(
			'step1'	=> array(
				'key1'	=> "value1",
				'key2'	=> "value2",
			),
		);
		$expected	= array(
			'step1.key1'	=> "value1",
			'step1.key2'	=> "value2",
		);
		$testTemplate	= new Template( $this->path.'template_testcase1.html' );
		$testTemplate->add( $tags );
		$actual	= self::getProtectedProperty( $testTemplate, 'elements' );
		self::assertEquals( $expected, $actual );
	}

	/**
	 * @return void
	 * @covers ::add
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAdd3(): void
	{
		$tags	= array(
			'step1.key1'	=> "value1",
			'step1.key2'	=> "value2",
		);
		$expected	= array(
			'step1.key1'	=> "value1",
			'step1.key2'	=> "value2",
		);
		$testTemplate	= new Template( $this->path.'template_testcase1.html' );
		$testTemplate->add( $tags );
		$actual	= self::getProtectedProperty( $testTemplate, 'elements' );
		self::assertEquals( $expected, $actual );
	}

	/**
	 * @return void
	 * @covers ::addElement
	 * @covers ::getElements
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAddElement(): void
	{
		$this->template->addElement( 'tag', 'name' );
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 1, $size );
		$elements = $this->template->getElements();
		self::assertEquals( 'name', $elements['tag'] );
	}

	/**
	 * @return void
	 * @covers ::addObject
	 * @covers ::getElements
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAddObject1(): void
	{
		$object	= new TemplateTestDataObject();
		$object->setData1( 'test1' );
		$this->template->addObject( 'dataObject', $object );
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 2, $size );

		$expected	= array(
			'dataObject.public'	=> 'test',
			'dataObject.data1'	=> 'test1'
		);
		$elements = $this->template->getElements();
		self::assertEquals( $expected, $elements );
	}

	/**
	 * @return void
	 * @covers ::addObject
	 * @covers ::getElements
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAddObject2(): void
	{
		$object	= new TemplateTestDataObject();
		$object->setData1( new ArrayObject( array( 'first', 'second' ) ) );
		$this->template->addObject( 'dataObject', $object );
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 3, $size );

		$expected	= array(
			'dataObject.public'		=> 'test',
			'dataObject.data1.0'	=> 'first',
			'dataObject.data1.1'	=> 'second'
		);
		$elements = $this->template->getElements();
		self::assertEquals( $expected, $elements );
	}

	/**
	 * @return void
	 * @covers ::addObject
	 * @covers ::getElements
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAddObject3(): void
	{
		$object	= new TemplateTestDataObject();
		$object->setData1( new Dictionary( array( 'key1' => 'val1', 'key2' => 'val2' ) ) );
		$this->template->addObject( 'dataObject', $object );
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 3, $size );

		$expected	= array(
			'dataObject.public'		=> 'test',
			'dataObject.data1.key1'	=> 'val1',
			'dataObject.data1.key2'	=> 'val2'
		);
		$elements = $this->template->getElements();
		self::assertEquals( $expected, $elements );
	}

	/**
	 * @return void
	 * @covers ::getElements
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetElements(): void
	{
		$data		= array( 'key' => 'value' );
		self::setProtectedProperty( $this->template, 'elements', $data );
		$expected	= $data;
		$actual	= $this->template->getElements();
		self::assertEquals( $expected, $actual );
	}

	/**
	 *	Tests Tags only
	 * @return void
	 * @covers ::addElement
	 * @covers ::render
	 * @covers ::setTemplate
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\Comments
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRender1(): void
	{
		$this->template->setTemplate( $this->path.'template_testcase1.html' );
		$this->template->addElement( 'title', 'das ist der titel' );
		$this->template->addElement( 'text', 'das ist der text' );
		$expected	= file_get_contents( $this->path.'template_testcase1_result.html' );
		$actual	= $this->template->render();
		self::assertEquals( $expected, $actual );
	}

	/**
	 *	Tests Comments only
	 * @return void
	 * @covers ::render
	 * @covers ::setTemplate
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\Comments
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRender2(): void
	{
		Template::addPlugin( new Comments() );
		$this->template->setTemplate( $this->path.'template_testcase2.html' );
		$expected	= file_get_contents( $this->path.'template_testcase2_result.html' );
		$actual	= $this->template->render();
/*		var_dump( $expected );
		var_dump( $actual );
*/
		self::assertEquals( $expected, $actual );
	}

	/**
	 *	Tests Nested Data Types only
	 * @return void
	 * @covers ::addElement
	 * @covers ::render
	 * @covers ::setTemplate
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\Comments
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRender3(): void
	{
		$this->template->setTemplate( $this->path.'template_testcase3.html' );
		$this->template->addElement( 'list', array( 1, 2, 3 ) );
		$this->template->addElement( 'array', array( 'key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3' ) );
		$expected	= file_get_contents( $this->path.'template_testcase3_result.html' );
		$actual	= $this->template->render();
		self::assertEquals( $expected, $actual );
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 * @return void
	 * @covers ::addElement
	 * @covers ::render
	 * @covers ::setTemplate
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\Comments
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRender4(): void
	{
		Template::addPlugin( new Comments() );
		$this->template->setTemplate( $this->path.'template_testcase4.html' );
		$this->template->addElement( 'title', 'das ist der titel' );
		$this->template->addElement( 'text', 'das ist der text' );
		$expected	= file_get_contents( $this->path.'template_testcase4_result.html' );
		$actual	= $this->template->render();
		self::assertEquals( $expected, $actual );
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 * @covers ::renderFile
	 * @covers \CeusMedia\TemplateEngine\PluginAbstract
	 * @covers \CeusMedia\TemplateEngine\Plugin\Comments
	 * @covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRenderFile(): void
	{
		Template::addPlugin( new Comments() );
		$data		= array(
			'title'	=> 'das ist der titel',
			'text'	=> 'das ist der text',
		);
		$expected	= file_get_contents( $this->path.'template_testcase4_result.html' );
		$actual		= Template::renderFile( $this->path.'template_testcase4.html', $data );
		self::assertEquals( $expected, $actual );
	}

	/**
	 *	Test renderString with basic elements.
	 *	@return void
	 *	@covers ::renderString
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRenderString(): void
	{
		$actual	= Template::renderString( '<%title%> <%name%>', [
			'title' => 'Mr',
			'name'  => 'Smith',
		] );
		self::assertEquals( 'Mr Smith', $actual );
	}

	/**
	 *	Test renderString with optional tags.
	 *	@return void
	 *	@covers ::renderString
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRenderString_Optional(): void
	{
		$actual	= Template::renderString( '<%title%> <%?missing%>', [
			'title' => 'Hello',
		] );
		self::assertEquals( 'Hello ', $actual );
	}

	/**
	 *	Test renderString throws on missing mandatory label.
	 *	@return void
	 *	@covers ::renderString
	 *	@covers \CeusMedia\TemplateEngine\Template
	 *	@covers \CeusMedia\TemplateEngine\Exception\Template
	 */
	public function testRenderString_MissingLabel(): void
	{
		$this->expectException( \CeusMedia\TemplateEngine\Exception\Template::class );
		Template::renderString( '<%title%> <%missing%>', [
			'title' => 'Hello',
		] );
	}

	/**
	 *	Test renderString with file name for error context.
	 *	@return void
	 *	@covers ::renderString
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRenderString_WithFileName(): void
	{
		$actual	= Template::renderString( '<%value%>', ['value' => 42], 'test.tmpl' );
		self::assertEquals( '42', $actual );
	}

	/**
	 *	Test caching with a mock PSR-16 cache.
	 *	@return void
	 *	@covers ::setCache
	 *	@covers ::setTemplate
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testSetCache(): void
	{
		$cache	= new MockCache();
		Template::setCache( $cache, 'test_' );
		$template	= new Template( $this->path.'template_testcase1.html' );
		$template->addElement( 'title', 'Test' );
		$template->addElement( 'text', 'Content' );
		$actual	= $template->render();
		self::assertIsString( $actual );
		self::assertNotEmpty( $cache->getStoredKeys() );
	}

	/**
	 *	Test checkLoadLimit throws when file loaded too many times.
	 *	@return void
	 *	@covers ::checkLoadLimit
	 *	@covers \CeusMedia\TemplateEngine\Template
	 *	@covers \CeusMedia\TemplateEngine\Exception\Template
	 */
	public function testCheckLoadLimit(): void
	{
		Template::clear();
		// Simulate loading a file more than 100 times
		$reflection	= new \ReflectionClass( Template::class );
		$method		= $reflection->getMethod( 'checkLoadLimit' );
		$method->setAccessible( TRUE );
		try {
			for( $i = 0; $i < 101; $i++ )
				$method->invoke( NULL, '/test/path/template.html' );
		} catch( \CeusMedia\TemplateEngine\Exception\Template $e ) {
			self::assertEquals( \CeusMedia\TemplateEngine\Exception\Template::FILE_LOAD_LIMIT, $e->getCode() );
			return;
		}
		self::fail( 'Expected TemplateException for load limit' );
	}

	/**
	 *	Test clear resets static state.
	 *	@return void
	 *	@covers ::clear
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testClear(): void
	{
		Template::clear();
		self::assertEquals( [], Template::$plugins );
		self::assertEquals( [], Template::$filters );
	}

	/**
	 *	Test getTemplatePath returns configured path.
	 *	@return void
	 *	@covers ::getTemplatePath
	 *	@covers ::setTemplatePath
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetTemplatePath(): void
	{
		Template::setTemplatePath( '/tmp/templates/' );
		self::assertEquals( '/tmp/templates/', Template::getTemplatePath() );
	}

	/**
	 *	Test setTemplatePath normalizes path.
	 *	@return void
	 *	@covers ::setTemplatePath
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testSetTemplatePath(): void
	{
		Template::setTemplatePath( '/tmp/templates' );
		self::assertEquals( '/tmp/templates', Template::getTemplatePath() );
	}

	/**
	 *	Test addFilter registers a filter with default keywords.
	 *	@return void
	 *	@covers ::addFilter
	 *	@covers \CeusMedia\TemplateEngine\Template
	 *	@covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testAddFilter(): void
	{
		Template::clear();
		$filter	= new \CeusMedia\TemplateEngine\Filter\Escape();
		Template::addFilter( $filter );
		self::assertArrayHasKey( 'escape', Template::$filters );
	}

	/**
	 *	Test addFilter with custom keywords.
	 *	@return void
	 *	@covers ::addFilter
	 *	@covers \CeusMedia\TemplateEngine\Template
	 *	@covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testAddFilter_CustomKeywords(): void
	{
		Template::clear();
		$filter	= new \CeusMedia\TemplateEngine\Filter\Escape();
		Template::addFilter( $filter, ['my_escape'] );
		self::assertArrayHasKey( 'my_escape', Template::$filters );
	}

	/**
	 *	Test addFilter throws on duplicate keyword.
	 *	@return void
	 *	@covers ::addFilter
	 *	@covers \CeusMedia\TemplateEngine\Template
	 *	@covers \CeusMedia\TemplateEngine\FilterAbstract
	 */
	public function testAddFilter_DuplicateKeyword(): void
	{
		Template::clear();
		$filter1	= new \CeusMedia\TemplateEngine\Filter\Escape();
		$filter2	= new \CeusMedia\TemplateEngine\Filter\Escape();
		Template::addFilter( $filter1 );
		$this->expectException( \RuntimeException::class );
		Template::addFilter( $filter2 );
	}

	/**
	 *	Test registerFilter registers a filter by class name.
	 *	@return void
	 *	@covers ::registerFilter
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRegisterFilter(): void
	{
		Template::clear();
		Template::registerFilter( \CeusMedia\TemplateEngine\Filter\Escape::class, ['html_escape'] );
		self::assertArrayHasKey( 'html_escape', Template::$filters );
		self::assertEquals( \CeusMedia\TemplateEngine\Filter\Escape::class, Template::$filters['html_escape'] );
	}

	/**
	 *	Test registerFilter throws on empty keywords.
	 *	@return void
	 *	@covers ::registerFilter
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRegisterFilter_EmptyKeywords(): void
	{
		Template::clear();
		$this->expectException( \InvalidArgumentException::class );
		Template::registerFilter( \CeusMedia\TemplateEngine\Filter\Escape::class, [] );
	}

	/**
	 *	Test registerFilter throws on duplicate keyword.
	 *	@return void
	 *	@covers ::registerFilter
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testRegisterFilter_DuplicateKeyword(): void
	{
		Template::clear();
		Template::registerFilter( \CeusMedia\TemplateEngine\Filter\Escape::class, ['dup'] );
		$this->expectException( \RuntimeException::class );
		Template::registerFilter( \CeusMedia\TemplateEngine\Filter\Escape::class, ['dup'] );
	}

	/**
	 *	Test __toString renders template.
	 *	@return void
	 *	@covers ::__toString
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testToString(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', '<%name%>' );
		self::setProtectedProperty( $template, 'elements', ['name' => 'Test'] );
		$actual	= (string) $template;
		self::assertEquals( 'Test', $actual );
	}

	/**
	 *	Test getTemplate returns template content.
	 *	@return void
	 *	@covers ::getTemplate
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetTemplate(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', 'Hello World' );
		self::assertEquals( 'Hello World', $template->getTemplate() );
	}

	/**
	 *	Test getLabels returns all labels.
	 *	@return void
	 *	@covers ::getLabels
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetLabels(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', '<%title%> <%?optional%> <%text%>' );
		$labels	= $template->getLabels();
		self::assertContains( '<%title%>', $labels );
		self::assertContains( '<%?optional%>', $labels );
		self::assertContains( '<%text%>', $labels );
	}

	/**
	 *	Test getLabels with mandatory type.
	 *	@return void
	 *	@covers ::getLabels
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetLabels_Mandatory(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', '<%title%> <%?optional%>' );
		$labels	= $template->getLabels( 1 );
		self::assertContains( '<%title%>', $labels );
		self::assertNotContains( '<%?optional%>', $labels );
	}

	/**
	 *	Test getLabels with optional type.
	 *	@return void
	 *	@covers ::getLabels
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetLabels_Optional(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', '<%title%> <%?optional%>' );
		$labels	= $template->getLabels( 2 );
		self::assertContains( '<%?optional%>', $labels );
		self::assertNotContains( '<%title%>', $labels );
	}

	/**
	 *	Test getLabels without XML delimiters.
	 *	@return void
	 *	@covers ::getLabels
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetLabels_NoXml(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', '<%title%>' );
		$labels	= $template->getLabels( 0, FALSE );
		self::assertContains( 'title', $labels );
	}

	/**
	 *	Test getTaggedComment returns comment content.
	 *	@return void
	 *	@covers ::getTaggedComment
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetTaggedComment(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', '<%--meta @title Test--%>' );
		$actual	= $template->getTaggedComment( 'meta' );
		self::assertNotNull( $actual );
		self::assertStringContainsString( '@title', $actual );
	}

	/**
	 *	Test getTaggedComment returns NULL when not found.
	 *	@return void
	 *	@covers ::getTaggedComment
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetTaggedComment_NotFound(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', '<%--meta @title Test--%>' );
		$actual	= $template->getTaggedComment( 'other' );
		self::assertNull( $actual );
	}

	/**
	 *	Test getTaggedComment without XML.
	 *	@return void
	 *	@covers ::getTaggedComment
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetTaggedComment_NoXml(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', '<%--meta @title Test--%>' );
		$actual	= $template->getTaggedComment( 'meta', FALSE );
		self::assertNotNull( $actual );
		self::assertStringNotContainsString( '<%--', $actual );
	}

	/**
	 *	Test getElementsFromComment returns elements.
	 *	@return void
	 *	@covers ::getElementsFromComment
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetElementsFromComment(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', "<%--meta\n@title Test\n@author John--%>" );
		$elements	= $template->getElementsFromComment( 'meta' );
		self::assertArrayHasKey( 'title', $elements );
		self::assertArrayHasKey( 'author', $elements );
	}

	/**
	 *	Test getElementsFromComment returns empty when not found.
	 *	@return void
	 *	@covers ::getElementsFromComment
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetElementsFromComment_NotFound(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', '<%--meta @title Test--%>' );
		$elements	= $template->getElementsFromComment( 'other' );
		self::assertEquals( [], $elements );
	}

	/**
	 *	Test getElementsFromComment with non-unique mode.
	 *	@return void
	 *	@covers ::getElementsFromComment
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testGetElementsFromComment_NonUnique(): void
	{
		$template	= new Template();
		self::setProtectedProperty( $template, 'template', "<%--meta\n@title First\n@title Second--%>" );
		$elements	= $template->getElementsFromComment( 'meta', FALSE );
		self::assertArrayHasKey( 'title', $elements );
		$titleVal	= $elements['title'];
		// First occurrence is a string, second makes it an array
		self::assertTrue( is_string( $titleVal ) || is_array( $titleVal ) );
	}

	/**
	 *	Test add with invalid key type throws.
	 *	@return void
	 *	@covers ::add
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAdd_InvalidKeyType(): void
	{
		$template	= new Template();
		$this->expectException( \TypeError::class );
		$template->add( [new \stdClass() => 'value'] );
	}

	/**
	 *	Test add with empty key throws.
	 *	@return void
	 *	@covers ::add
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAdd_EmptyKey(): void
	{
		$template	= new Template();
		$this->expectException( \InvalidArgumentException::class );
		$template->add( ['' => 'value'] );
	}

	/**
	 *	Test add with resource value type throws.
	 *	@return void
	 *	@covers ::add
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAdd_UnsupportedType(): void
	{
		$template	= new Template();
		$this->expectException( \InvalidArgumentException::class );
		$resource	= tmpfile();
		$template->add( ['key' => $resource] );
		fclose( $resource );
	}

	/**
	 *	Test add with null value skips the element.
	 *	@return void
	 *	@covers ::add
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAdd_NullValue(): void
	{
		$template	= new Template();
		$count	= $template->add( ['key' => null] );
		self::assertEquals( 0, $count );
	}

	/**
	 *	Test add with boolean value.
	 *	@return void
	 *	@covers ::add
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAdd_BooleanValue(): void
	{
		$template	= new Template();
		$count	= $template->add( ['flag' => true] );
		self::assertEquals( 1, $count );
	}

	/**
	 *	Test add with overwrite flag.
	 *	@return void
	 *	@covers ::add
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAdd_Overwrite(): void
	{
		$template	= new Template();
		$template->add( ['key' => 'first'] );
		$template->add( ['key' => 'second'], TRUE );
		$elements	= $template->getElements();
		self::assertEquals( 'second', $elements['key'] );
	}

	/**
	 *	Test addFilter with filter applied during render.
	 *	@return void
	 *	@covers ::addFilter
	 *	@covers ::render
	 *	@covers \CeusMedia\TemplateEngine\Template
	 *	@covers \CeusMedia\TemplateEngine\FilterAbstract
	 *	@covers \CeusMedia\TemplateEngine\Filter\Rot13
	 */
	public function testAddFilter_Render(): void
	{
		Template::clear();
		$filter	= new \CeusMedia\TemplateEngine\Filter\Rot13();
		Template::addFilter( $filter );
		$actual	= Template::renderString( '<%text|rot13%>', ['text' => 'Hello'] );
		self::assertEquals( str_rot13( 'Hello' ), $actual );
	}

	/**
	 *	Test Exception/Template getFilePath.
	 *	@return void
	 *	@covers \CeusMedia\TemplateEngine\Exception\Template::__construct
	 *	@covers \CeusMedia\TemplateEngine\Exception\Template::getFilePath
	 *	@covers \CeusMedia\TemplateEngine\Exception\Template::getNotUsedLabels
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testExceptionGetFilePath(): void
	{
		$e	= new \CeusMedia\TemplateEngine\Exception\Template(
			\CeusMedia\TemplateEngine\Exception\Template::FILE_NOT_FOUND,
			'/path/to/template.html'
		);
		self::assertEquals( '/path/to/template.html', $e->getFilePath() );
	}

	/**
	 *	Test Exception/Template getNotUsedLabels.
	 *	@return void
	 *	@covers \CeusMedia\TemplateEngine\Exception\Template::__construct
	 *	@covers \CeusMedia\TemplateEngine\Exception\Template::getNotUsedLabels
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testExceptionGetNotUsedLabels(): void
	{
		$labels	= ['title', 'name'];
		$e	= new \CeusMedia\TemplateEngine\Exception\Template(
			\CeusMedia\TemplateEngine\Exception\Template::LABELS_MISSING,
			NULL,
			$labels
		);
		self::assertEquals( $labels, $e->getNotUsedLabels() );
	}

	/**
	 *	Test FilterAbstract::setKeywords replaces keywords.
	 *	@return void
	 *	@covers \CeusMedia\TemplateEngine\FilterAbstract::setKeywords
	 *	@covers \CeusMedia\TemplateEngine\FilterAbstract
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testFilterAbstractSetKeywords(): void
	{
		$filter	= new \CeusMedia\TemplateEngine\Filter\Escape();
		$filter->setKeywords( ['custom'] );
		self::assertEquals( ['custom'], $filter->getKeywords() );
	}

	/**
	 *	Test FilterAbstract::setKeywords appends keywords.
	 *	@return void
	 *	@covers \CeusMedia\TemplateEngine\FilterAbstract::setKeywords
	 *	@covers \CeusMedia\TemplateEngine\FilterAbstract
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testFilterAbstractSetKeywordsAppend(): void
	{
		$filter	= new \CeusMedia\TemplateEngine\Filter\Escape();
		$filter->setKeywords( ['custom'], TRUE );
		$keywords	= $filter->getKeywords();
		self::assertContains( 'escape', $keywords );
		self::assertContains( 'custom', $keywords );
	}

	/**
	 *	Test PluginAbstract::getOptions returns options.
	 *	@return void
	 *	@covers \CeusMedia\TemplateEngine\PluginAbstract::getOptions
	 *	@covers \CeusMedia\TemplateEngine\PluginAbstract
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testPluginAbstractGetOptions(): void
	{
		$plugin	= new \CeusMedia\TemplateEngine\Plugin\Comments( ['remove' => TRUE] );
		$options	= $plugin->getOptions();
		self::assertTrue( $options['remove'] );
	}

	/**
	 *	Test addTemplate adds a nested template.
	 *	@return void
	 *	@covers ::addTemplate
	 *	@covers \CeusMedia\TemplateEngine\Template
	 */
	public function testAddTemplate(): void
	{
		$template	= new Template();
		$template->addTemplate( 'nested', $this->path.'template_testcase1.html', ['title' => 'Nested', 'text' => 'Content'] );
		$elements	= $template->getElements();
		self::assertArrayHasKey( 'nested', $elements );
		self::assertInstanceOf( Template::class, $elements['nested'] );
	}

	/**
	 *	Test addPlugin with Matrix plugin keyword conflict.
	 *	@return void
	 *	@covers ::addPlugin
	 *	@covers \CeusMedia\TemplateEngine\Template
	 *	@covers \CeusMedia\TemplateEngine\PluginAbstract
	 *	@covers \CeusMedia\TemplateEngine\Plugin\Matrix
	 */
	public function testAddPlugin_DuplicateKeyword(): void
	{
		Template::clear();
		$p1	= new \CeusMedia\TemplateEngine\Plugin\Matrix();
		$p2	= new \CeusMedia\TemplateEngine\Plugin\Matrix();
		Template::addPlugin( $p1 );
		$this->expectException( \RuntimeException::class );
		Template::addPlugin( $p2 );
	}

	/**
	 *	Returns the value of a protected property via reflection.
	 *	@param		object		$object		Object instance
	 *	@param		string		$propertyName	Property name
	 *	@return		mixed
	 */
	protected static function getProtectedProperty( object $object, string $propertyName ): mixed
	{
		$reflection	= new ReflectionObject( $object );
		$property		= $reflection->getProperty( $propertyName );
		return $property->getValue( $object );
	}

	/**
	 *	Sets the value of a protected property via reflection.
	 *	@param		object		$object		Object instance
	 *	@param		string		$propertyName	Property name
	 *	@param		mixed		$value			Value to set
	 *	@return		void
	 */
	protected static function setProtectedProperty( object $object, string $propertyName, mixed $value ): void
	{
		$reflection	= new ReflectionObject( $object );
		$property		= $reflection->getProperty( $propertyName );
		$property->setValue( $object, $value );
	}
}

class TemplateTestDataObject
{
	public string $public	= "test";
	protected mixed $data1	= NULL;

	/**
	 * @return mixed
	 */
	public function getData1(): mixed
	{
		return $this->data1;
	}

	/**
	 * @param mixed $value
	 * @return $this
	 */
	public function setData1( mixed $value ): self
	{
		$this->data1	= $value;
		return $this;
	}
}
