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

	/** @var object $mock */
	protected object $mock;
	protected array $mockElements;
	protected string $path;

	public function setUp(): void
	{
		$this->mock			= MockAntiProtection::getInstance( Template::class );
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
		$actual	= $this->mock->add( $this->mockElements );
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
		$this->mock->add( $tags );
		$actual	= $this->mock->getProtectedVar( 'elements' );
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
		$this->mock->add( $tags );
		$actual	= $this->mock->getProtectedVar( 'elements' );
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
		$this->mock->setProtectedVar( 'elements', $data );
		$expected	= $data;
		$actual	= $this->mock->getElements();
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
