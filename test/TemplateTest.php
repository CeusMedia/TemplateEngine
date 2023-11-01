<?php
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare( strict_types = 1 );

/**
 *	TestUnit of UI_Template
 *	@package		tests.ui
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 */

namespace CeusMedia\CommonTest\UI;

use CeusMedia\Common\ADT\Collection\Dictionary;
use CeusMedia\TemplateEngine\Plugin\Comments;
use CeusMedia\TemplateEngine\Template;
use CeusMedia\CommonTest\BaseCase;
use CeusMedia\CommonTest\MockAntiProtection;

use ArrayObject;

/**
 *	TestUnit of UI_Template
 *	@package		tests.ui
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 */
class TemplateTest extends BaseCase
{
	private Template $template;
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

	public function testInitiallyNoElements(): void
	{
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 0, $size );
	}

	public function testAdd1(): void
	{
		$assertion	= 18;
		/** @phpstan-ignore-next-line */
		$creation	= $this->mock->add( $this->mockElements );
		self::assertEquals( $assertion, $creation );
	}

	public function testAdd2(): void
	{
		$tags	= array(
			'step1'	=> array(
				'key1'	=> "value1",
				'key2'	=> "value2",
			),
		);
		$assertion	= array(
			'step1.key1'	=> "value1",
			'step1.key2'	=> "value2",
		);
		/** @phpstan-ignore-next-line */
		$this->mock->add( $tags );
		/** @phpstan-ignore-next-line */
		$creation	= $this->mock->getProtectedVar( 'elements' );
		self::assertEquals( $assertion, $creation );
	}

	public function testAdd3(): void
	{
		$tags	= array(
			'step1.key1'	=> "value1",
			'step1.key2'	=> "value2",
		);
		$assertion	= array(
			'step1.key1'	=> "value1",
			'step1.key2'	=> "value2",
		);
		/** @phpstan-ignore-next-line */
		$this->mock->add( $tags );
		/** @phpstan-ignore-next-line */
		$creation	= $this->mock->getProtectedVar( 'elements' );
		self::assertEquals( $assertion, $creation );
	}

	public function testAddElement(): void
	{
		$this->template->addElement( 'tag', 'name' );
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 1, $size );
		$elements = $this->template->getElements();
		self::assertEquals( 'name', $elements['tag'] );
	}

	public function testAddObject1(): void
	{
		$object	= new TemplateTestDataObject();
		$object->setData1( 'test1' );
		$this->template->addObject( 'dataObject', $object );
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 2, $size );

		$assertion	= array(
			'dataObject.public'	=> 'test',
			'dataObject.data1'	=> 'test1'
		);
		$elements = $this->template->getElements();
		self::assertEquals( $assertion, $elements );
	}

	public function testAddObject2(): void
	{
		$object	= new TemplateTestDataObject();
		$object->setData1( new ArrayObject( array( 'first', 'second' ) ) );
		$this->template->addObject( 'dataObject', $object );
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 3, $size );

		$assertion	= array(
			'dataObject.public'		=> 'test',
			'dataObject.data1.0'	=> 'first',
			'dataObject.data1.1'	=> 'second'
		);
		$elements = $this->template->getElements();
		self::assertEquals( $assertion, $elements );
	}

	public function testAddObject3(): void
	{
		$object	= new TemplateTestDataObject();
		$object->setData1( new Dictionary( array( 'key1' => 'val1', 'key2' => 'val2' ) ) );
		$this->template->addObject( 'dataObject', $object );
		$size	= sizeof( $this->template->getElements() );
		self::assertEquals( 3, $size );

		$assertion	= array(
			'dataObject.public'		=> 'test',
			'dataObject.data1.key1'	=> 'val1',
			'dataObject.data1.key2'	=> 'val2'
		);
		$elements = $this->template->getElements();
		self::assertEquals( $assertion, $elements );
	}

	public function testGetElements(): void
	{
		$data		= array( 'key' => 'value' );
		/** @phpstan-ignore-next-line */
		$this->mock->setProtectedVar( 'elements', $data );
		$assertion	= $data;
		/** @phpstan-ignore-next-line */
		$creation	= $this->mock->getElements();
		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	Tests Tags only
	 */
	public function testRender1(): void
	{
		$this->template->setTemplate( $this->path.'template_testcase1.html' );
		$this->template->addElement( 'title', 'das ist der titel' );
		$this->template->addElement( 'text', 'das ist der text' );
		$assertion	= file_get_contents( $this->path.'template_testcase1_result.html' );
		$creation	= $this->template->render();
		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	Tests Comments only
	 */
	public function testRender2(): void
	{
		Template::addPlugin( new Comments() );
		$this->template->setTemplate( $this->path.'template_testcase2.html' );
		$assertion	= file_get_contents( $this->path.'template_testcase2_result.html' );
		$creation	= $this->template->render();
		/*		var_dump( $assertion );
				var_dump( $creation );
		*/		self::assertEquals( $assertion, $creation );
	}

	/**
	 *	Tests Nested Data Types only
	 */
	public function testRender3(): void
	{
		$this->template->setTemplate( $this->path.'template_testcase3.html' );
		$this->template->addElement( 'list', array( 1, 2, 3 ) );
		$this->template->addElement( 'array', array( 'key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3' ) );
		$assertion	= file_get_contents( $this->path.'template_testcase3_result.html' );
		$creation	= $this->template->render();
		self::assertEquals( $assertion, $creation );
	}

	public function testRender4(): void
	{
		Template::addPlugin( new Comments() );
		$this->template->setTemplate( $this->path.'template_testcase4.html' );
		$this->template->addElement( 'title', 'das ist der titel' );
		$this->template->addElement( 'text', 'das ist der text' );
		$assertion	= file_get_contents( $this->path.'template_testcase4_result.html' );
		$creation	= $this->template->render();
		self::assertEquals( $assertion, $creation );
	}

	public function testRenderFile(): void
	{
		Template::addPlugin( new Comments() );
		$data		= array(
			'title'	=> 'das ist der titel',
			'text'	=> 'das ist der text',
		);
		$assertion	= file_get_contents( $this->path.'template_testcase4_result.html' );
		$creation	= Template::renderFile( $this->path.'template_testcase4.html', $data );
		self::assertEquals( $assertion, $creation );
	}
}

class TemplateTestDataObject
{
	public string $public		= "test";
	protected $data1	= NULL;

	public function getData1()
	{
		return $this->data1;
	}

	public function setData1( $value ): void
	{
		$this->data1	= $value;
	}
}