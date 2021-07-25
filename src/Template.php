<?php
/**
 *	Template Class.
 *
 *	Copyright (c) 2007-2021 Christian Würker (ceusmedia.de)
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
 *	@package		CeusMedia_TemplateEngine
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2021 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
namespace CeusMedia\TemplateEngine;

use CeusMedia\Cache\AdapterInterface as CacheAdapterInterface;
use CeusMedia\TemplateEngine\Exception\Template as TemplateException;
use CeusMedia\TemplateEngine\Plugin\Matrix as MatrixPlugin;
use Alg_Object_MethodFactory as MethodFactory;
use InvalidArgumentException;
use FS_File_Reader as FileReader;
use Alg_Object_Factory as ObjectFactory;
use ReflectionObject;
use RuntimeException;

use function method_exists;

/**
 *	Template Class.
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2021 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
class Template
{
	/**	@var		array		$plugins		List of template plugin instances */
	public static $plugins		= [];

	/**	@var		array		$filters		List of template filter instances */
	public static $filters		= [];

	/**	@var		CacheAdapterInterface|NULL	$cache		Storage instance to be used for caching */
	protected static $cache		= NULL;

	/**	@var		string		$cachePrefix	... */
	protected static $cachePrefix;

	/**	@var		string		$pathTemplates	... */
	protected static $pathTemplates	= '';

	/**	@var		array		$loaded			List of loaded templates, used to avoid to load templates to often */
	protected static $loaded		= [];

	/**	@var		string		$className		Name of template class */
	protected $className;

	/**	@var		array		$elements		the first dimension holds all added labels, the second dimension holds elements for each label */
	protected $elements;

	/**	@var		string		$fileName		Filename of a specified template file */
	protected $fileName;

	/**	@var		string		$template		Content of a specified template file */
	protected $template;

	/**	@var		string		$tmp			... */
	protected $tmp;

	/**
	 *	Registers a filter instance, which can be applied to all template tags.
	 *	If no keywords are given the filter's default keywords are registered.
	 *	You can use 'registerFilter' to avoid building an instance.
	 *	@access		public
	 *	@param		FilterInterface	$filter		Instance of filter
	 *	@param		array			$keywords	List of keywords to bind filter on
	 *	@return		void
	 */
	public static function addFilter( FilterInterface $filter, array $keywords = [] )
	{
		if( !$keywords )
			$keywords	= $filter->getKeywords();
		foreach( $keywords as $keyword ){
			if( array_key_exists( $keyword, self::$filters ) )
				throw new RuntimeException( 'Filter keyword "'.$keyword.'" is already taken by another filter' );
			self::$filters[$keyword]	= $filter;
		}
	}

	/**
	 *	Registers a plugin instance, which can be applied to all template tags.
	 *	@access		public
	 *	@param		PluginInterface	$plugin		Instance of filter
	 *	@return		void
	 */
	public static function addPlugin( PluginInterface $plugin )
	{
		$keyword	= $plugin->getKeyword();
		$priority	= $plugin->getPriority();
		foreach( self::$plugins as $pluginsPriority => $plugins )
			foreach( $plugins as $pluginInstance )
				if( $pluginInstance instanceof MatrixPlugin )
					if( $pluginInstance->getKeyword() == $keyword )
						throw new RuntimeException( 'Plugin keyword "'.$keyword.'" is already taken by another plugin' );
		if( !isset( self::$plugins[$priority] ) )
			self::$plugins[$priority]	= [];
		self::$plugins[$priority][]	= $plugin;
	}

	/**
	 *	...
	 *	@static
	 *	@access		public
	 *	@return		string
	 */
	public static function getTemplatePath(): string
	{
		return self::$pathTemplates;
	}

	/**
	 *	Registers a filter, which can be applied to all template tags, by its class name.
	 *	@access		public
	 *	@param		string		$className	Name of filter class, implementing CMM_STE_Filter_Interface
	 *	@param		array		$keywords	List of keywords to bind filter to
	 *	@return		void
	 */
	public static function registerFilter( string $className, array $keywords )
	{
		if( 0 === count( $keywords ) )
			throw new InvalidArgumentException( 'No filter keywords given' );
		foreach( $keywords as $keyword ){
			if( array_key_exists( $keyword, self::$filters ) )
				throw new RuntimeException( 'Filter keyword "'.$keyword.'" is already taken by another filter' );
			self::$filters[$keyword]	= $className;
		}
	}

	/**
	 *	Renders a template file with given elements statically.
	 *	@access		public
	 *	@static
	 *	@param		string		$fileName		File Name of Template File
	 *	@param		array		$elements		List of Elements {@link add()}
	 *	@return		string
	 */
	public static function renderFile( string $fileName, array $elements = [] ): string
	{
		$template	= new self( $fileName, $elements );
		return $template->render();
	}

	/**
	 *	Renders a template string with given elements statically.
	 *	@access		public
	 *	@static
	 *	@param		string		$string			Template String
	 *	@param		array		$elements		Map of Elements for Template String
	 *	@param		string		$fileName		File name of the template. Needed in case of error of missing labels
	 *	@return		string
	 */
	public static function renderString( string $string, array $elements = [], string $fileName = NULL )
	{
		$template				= new self();
		$template->template		= $string;
		if( NULL !== $fileName )
			$template->fileName		= $fileName;
		$template->add( $elements );
		return $template->render();
	}

	/**
	 *	Sets storage for caching.
	 *	@access		public
	 *	@static
	 *	@param		CacheAdapterInterface	$storage		Storage instance to be used for caching
	 *	@param		string						$prefix			Prefix for keys in cache.
	 *	@return		void
	 */
	public static function setCache( CacheAdapterInterface $storage, string $prefix = '' )
	{
		self::$cache		= $storage;
		self::$cachePrefix	= $prefix;
	}

	/**
	 *	Sets path to templates.
	 *	@access		public
	 *	@param		string		$path		Path to templates
	 *	@return		void
	 */
	public static function setTemplatePath( string $path )
	{
		self::$pathTemplates	= (string) preg_replace( "@(.+)/$@", "\\1/", $path );
	}

	/**
	 *	Constructor
	 *	@access		public
	 *	@param		string|NULL	$fileName		File Name of Template File
	 *	@param		array		$elements		List of Elements {@link add()}
	 *	@return		void
	 */
	public function __construct( ?string $fileName = NULL, array $elements = [] )
	{
		$this->elements		= [];
		$this->className	= get_class( $this );
		if( NULL !== $fileName )
			$this->setTemplate( $fileName );
		$this->add( $elements );
	}

	/**
	 *	...
	 *	@access		public
	 *	@return		string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 *	Adds an associative array with labels and elements to the template and returns number of added elements.
	 *	@param		array 		$elements	Array where the <b>key</b> can be a string, integer or
	 *										float and is the <b>label</b>. The <b>value</b> can be a
	 *										string, integer, float or a template object and represents
	 *										the element to add.
	 *	@param		boolean		$overwrite	if TRUE an a tag is already used, it will overwrite it
	 *	@return		integer
	 */
	public function add( array $elements, bool $overwrite = FALSE )
	{
		if( !is_array( $elements ) )
			return 0;
		$number	= 0;
		foreach( $elements as $key => $value ){
			$type = gettype( $key );
			if( !in_array( $type, ['string', 'integer', 'double'] ) )
				throw new InvalidArgumentException( 'Invalid key type "'.gettype( $key ).'"' );
			if( !strlen( trim( $key ) ) )
				throw new InvalidArgumentException( 'Key cannot be empty' );

			$isListObject	= $value instanceof \ArrayObject || $value instanceof \ADT_List_Dictionary;
			$isPrimitive	= is_string( $value ) || is_int( $value ) || is_float( $value ) || is_bool( $value );
			$isTemplate		= $value instanceof $this->className;
			if( is_null( $value ) )
				continue;
			else if( is_array( $value ) || $isListObject )
				$number	+= $this->addArrayRecursive( $key, $value, [], $overwrite );
			else if( $isPrimitive || $isTemplate ){
//				if( $overwrite == TRUE )
//					$this->elements[$key] = [];
//				$this->elements[$key][] = $value;
				$this->elements[$key] = $value;
				$number	++;
			}
			else if( is_object( $value ) )
				$this->addObject( $key, $value, [], $overwrite );
			else
				throw new InvalidArgumentException( 'Unsupported type '.gettype( $value ).' for "'.$key.'"' );
		}
		return $number;
	}

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$name		...
	 *	@param		object		$object		...
	 *	@param		array		$steps		...
	 *	@param		boolean		$overwrite		...
	 *	@return		int
	 */
	public function addObject( string $name, $object, array $steps = [], bool $overwrite = FALSE ): int
	{
		$number		= 0;
		$steps[]	= $name;
		$reflection	= new ReflectionObject( $object );
		foreach( $reflection->getProperties() as $property ){
			$key		= $property->getName();
			$methodName	= 'get'.ucfirst( $key );
			if( $property->isPublic() )
				$value	= $property->getValue( $object );
			else if( $reflection->hasMethod( $methodName ) )
				$value	= MethodFactory::callObjectMethod( $object, $methodName );
			else
				continue;
			$label	= implode( ".", $steps ).".".$key;
			$this->addElement( $label, $value, $overwrite );
			$number ++;
		}
		return $number;
	}

	/**
	 *	Adds an array recursive and returns number of added elements.
	 *	@access		public
	 *	@param		string		$name			Key of array
	 *	@param		mixed		$data			Values of array
	 *	@param		array		$steps			Steps within recursion
	 *	@param		bool		$overwrite		Flag: overwrite existing tag
	 *	@return		int
	 */
	public function addArrayRecursive( $name, $data, array $steps = [], bool $overwrite = FALSE ): int
	{
		$number		= 0;
		$steps[]	= $name;
		foreach( $data as $key => $value ){
			$isListObject	= $value instanceof \ArrayObject || $value instanceof \ADT_List_Dictionary;
			if( is_array( $value ) || $isListObject  ){
				$number	+= $this->addArrayRecursive( $key, $value, $steps );
			}
			else{
				$key	= implode( ".", $steps ).".".$key;
				$this->addElement( $key, $value );
				$number ++;
			}
		}
		return $number;
	}

	/**
	 *	Adds one Element.
	 *	@param		string							$tag		Tag name
	 *	@param		string|integer|float|Template	$element	...
	 *	@param		boolean							$overwrite	if set to TRUE, it will overwrite an existing element with the same label
	 *	@return		void
	 */
	public function addElement( string $tag, $element, bool $overwrite = FALSE )
	{
		$this->add( [$tag => $element], $overwrite );
	}

	/**
	 *	Adds another Template.
	 *	@access		public
	 *	@param		string		$tag		tagname
	 *	@param		string		$fileName	template file
	 *	@param		array		$elements	array containing elements {@link add()}
	 *	@param		boolean		$overwrite	if set to TRUE, it will overwrite an existing element with the same label
	 *	@return		void
	 */
	public function addTemplate( string $tag, string $fileName, array $elements = [], bool $overwrite = FALSE )
	{
		$this->addElement( $tag, new self( $fileName, $elements ), $overwrite );
	}

	/**
	 *	Returns all registered Elements.
	 *	@access		public
	 *	@return		array		all set elements
	 */
	public function getElements(): array
	{
		return $this->elements;
	}

	/**
	 *	Returns all marked elements from a comment.
	 *	@param		string		$comment		Comment Tag
	 *	@param		boolean		$unique			Flag: unique Keys only
	 *	@return		array						containing Elements or empty
	 */
	public function getElementsFromComment( string $comment, bool $unique = TRUE ): array
	{
		$content = $this->getTaggedComment( $comment );
		if( !isset( $content ) )
			return [];

		$list	= [];
		$content = explode( "\n", $content );
		foreach( $content as $row ){
			if( (bool) preg_match( '/\s*@(\S+)?\s+(.*)/', $row, $out ) ){
				if( $unique )
					$list[$out[1]] = $out[2];
				else
					$list[$out[1]][] = $out[2];
			}
		}
		return $list;
	}

	/**
	 *	Returns all defined labels.
	 *	@param		integer		$type		Label Type: 0=all, 1=mandatory, 2=optional
	 *	@param		boolean		$xml		Flag: with or without delimiter
	 *	@return		array					Array of Labels
	 */
	public function getLabels( int $type = 0, bool $xml = TRUE ): array
	{
 		$content = (string) preg_replace( '/<%\??--.*--%>/sU', '', $this->template );
		switch( $type ){
			case 2:
				preg_match_all( '/<%(\?.*)%>/U', $content, $tags );
				break;
			case 1:
				preg_match_all( '/<%([^-?].*)%>/U', $content, $tags );
				break;
			default:
				preg_match_all( '/<%([^-].*)%>/U', $content, $tags );
		}
		return $xml ? $tags[0] : $tags[1];
	}

	/**
	 *	Returns a tagged comment.
	 *	@param		string		$tag		Comment Tag
	 *	@param		boolean		$xml		Flag: with or without Delimiter
	 *	@return		string|NULL				Comment or NULL
	 *	@todo		quote specialchars in tagname
	 */
	public function getTaggedComment( string $tag, bool $xml = TRUE ): ?string
	{
		if( preg_match( '/<%--'.$tag.'(.*)--%>/sU', $this->template, $tag ) )
			return $xml ? $tag[0] : $tag[1];
		return '';
	}

	/**
	 *	Returns loaded Template.
	 *	@return		string|NULL		template content
	 */
	public function getTemplate(): ?string
	{
		return $this->template;
	}

	/**
	 *	Renders output of given template file or string with applied elements, filters and plugins.
	 *	All labels will be replaced with apropriate elements.
	 *	All registered plugins will ... (be applied before and after label replacement.
	 *	All registered filters will ... (be applied
	 *	If a non optional label wasn't specified, the method will throw a Template Exception
	 *	@access		public
	 *	@return		string
	 */
	public function render(): string
	{
		$out			= $this->template;															//  local copy of set template content
		$callbackFilter	= [$this, 'applyFilters'];													//  create callback for applying filters on replacement of element labels

		$this->applyPlugins( $out, 'pre' );															//  apply pre processing plugins

		foreach( $this->elements as $label => $element ){											//  iterate over all registered element containers
			$tmp = '';																				//
//			foreach( $labelElements as $element ){													//  iterate over all elements with current element container
	 			if( is_object( $element ) ){														//  element is an object
	 				if( !( $element instanceof $this->className ) )									//  object is not an template of this template engine
						continue;																	//  skip this one
					if( method_exists( $element, 'create' ) )
						$element = $element->create();												//  render template before concat
	 			}
				$tmp	.= $element;
//			}
			$this->tmp	= $tmp;																		//  store current temporary element content for filters
			$pattern	= '/<%(\?)?('.preg_quote( $label, '/' ).')(\|.+)?%>/';						//  create regular expression for element label with filter support
			$out		= preg_replace_callback( $pattern, $callbackFilter, $out );					//  realize placeholder, apply filters on content
 		}
		$out = preg_replace( '/<%\?.*%>/U', '', $out );    											//  remove left over optional placeholders
		$out = preg_replace( '/\n\s+\n/', "\n", $out );												//  remove double line breaks

		$this->applyPlugins( $out, 'post' );														//  apply post processing plugins

		$out = preg_replace( '/<%\??\w+\(.+\)%>/U', '', $out );    									//  remove left over plugin calls @todo kriss: handle this with exceptions later

		$tags = [];																			//  create container for left over placeholders
		if( preg_match_all( '/<%.*%>/U', $out, $tags ) === 0 )										//  no more placeholders left over
			return $out; 																			//  return final result

		$tags	= array_shift( $tags );																//
		foreach( $tags as $key => $value )															//
			$tags[$key]	= preg_replace( '@(<%\??)|%>@', "", $value );								//
		if( $this->fileName )																		//
			throw new TemplateException(
				TemplateException::FILE_LABELS_MISSING,
				$this->fileName,
				$tags
			);
		throw new TemplateException(
			TemplateException::LABELS_MISSING,
			NULL,
			$tags
		);
	}

	/**
	 *	Loads a new template file if it exists. Otherwise it will throw an Exception.
	 *	@param		string|NULL		$fileName 	File Name of Template
	 *	@return		boolean
	 */
	public function setTemplate( ?string $fileName = NULL ): bool
	{
		if( empty( $fileName ) )																	//  no file name given
			return FALSE;																			//  return with negative result

		$filePath	= self::$pathTemplates.$fileName;												//  get file within set file path
		self::checkLoadLimit( $filePath );															//  check load limit for this template
		$this->fileName	= $fileName;																//  set template file name, needed for exception handling

		if( NULL !== self::$cache ){																//  cache is enabled
			if( self::$cache->has( self::$cachePrefix.$filePath ) ){								//  cache has hit on template file
				$this->template = self::$cache->get( self::$cachePrefix.$filePath );				//  set template content
				return TRUE;																		//  return with positive result
			}
		}

		if( !file_exists( $filePath ) )															//  file is not existing
			throw new TemplateException( TemplateException::FILE_NOT_FOUND, $filePath );		//  break with exception
		$content	= FileReader::load( $filePath );										//  load file content
		if( NULL !== self::$cache )																		//  cache is enabled
			self::$cache->set( self::$cachePrefix.$filePath, $content );						//  store file content in cache

		if( !file_exists( $filePath ) )																//  file is not existing
			throw new Exception\Template( Exception\Template::FILE_NOT_FOUND, $filePath );			//  break with exception
		$content	= \FS_File_Reader::load( $filePath );											//  load file content
		if( NULL !== self::$cache )																	//  cache is enabled
			self::$cache->set( self::$cachePrefix.$filePath, $content );							//  store file content in cache
		$this->template = $content;																	//  set template content
		return TRUE;																				//  return with positive result
	}

	//  --  PROTECTED  --  //

	/**
	 *	Counts loadings of template files and excepts if a file has been loaded too often.
	 *	@static
	 *	@access		protected
	 *	@param		string		$filePath		...
	 *	@return		void
	 *	@throws		TemplateException
	 *	@todo		make limit configurable
	 */
	protected static function checkLoadLimit( string $filePath )
	{
		if( !in_array( $filePath, self::$loaded ) )													//  file not found in load list
			self::$loaded[$filePath] = 1;															//  append file to load list
		else
			self::$loaded[$filePath]++;																//  count file load
		if( self::$loaded[$filePath] > 100 ){														//  file loaded 100 times
			throw new TemplateException(															//  break because limit is reached
				TemplateException::FILE_LOAD_LIMIT,
				$filePath
			);
		}
	}

	/**
	 *	...
	 *	@access		protected
	 *	@param		array		$matches		...
	 *	@return		string
	 */
	protected function applyFilters( array $matches ): string
	{
		$filters	= [];
		if( empty( $matches[3] ) )
			return $this->tmp;
		foreach( explode( '|', $matches[3] ) as $filter ){
			if( trim( $filter ) ){
				$parts		= explode( ':', trim( $filter ) );
				$filter		= $parts[0];
				$arguments	= isset( $parts[1] ) ? explode( ',', $parts[1] ) : [];
				if( array_key_exists( $filter, self::$filters ) ){
					if(	is_string( self::$filters[$filter] ) )
						self::$filters[$filter]	= ObjectFactory::createObject( self::$filters[$filter] );
					$this->tmp	= self::$filters[$filter]->apply( $this->tmp, $arguments );
				}
			}
		}
		return $this->tmp;
	}

	/**
	 *	Applies registered plugins directly to current template content.
	 *	@access		protected
	 *	@param		string		$content		Reference to current template content
	 *	@param		string		$type			Type of plugins to apply: pre | post
	 *	@return		string
	 */
	protected function applyPlugins( &$content, $type = 'pre' ): string
	{
		ksort( self::$plugins );
		foreach( self::$plugins as $priority => $plugins )											//  iterate plugins priorities
			foreach( $plugins as $plugin )															//  iterate plugins in priority
				if( $plugin->getType() == $type )													//  plugin type is matching
					$content	= $plugin->work( $content, $this->elements );						//  apply plugin on template content
		return $content;
	}
}
