<?php
/**
 *	Template Class.
 *
 *	Copyright (c) 2007-2011 Christian Würker (ceus-media.de)
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
 *	@category		cmClasses
 *	@package		UI
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceus-media.de>
 *	@copyright		2007-2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmclasses/
 *	@since			03.03.2007
 *	@version		$Id: Template.php5 837 2011-03-08 15:50:28Z christian.wuerker $
 */
/**
 *	Template Class.
 *	@category		cmClasses
 *	@package		UI
 *	@uses			Exception_Template
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceus-media.de>
 *	@copyright		2007-2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmclasses/
 *	@since			03.03.2007
 *	@version		$Id: Template.php5 837 2011-03-08 15:50:28Z christian.wuerker $
 */
class CMM_STE_Template{

	/**	@var		string		$className		Name of template class */
	protected $className;
	/**	@var		array		$elements		the first dimension holds all added labels, the second dimension holds elements for each label */
	protected $elements;
	/**	@var		string		$fileName		Filename of a specified template file */
	protected $fileName;
	/**	@var		string		$template		Content of a specified template file */
	protected $template;
	/**	@var		CMM_SEA_Adapter_Interface	$storage		Storage instance to be used for caching */
	protected static $cache;
	protected static $cachePrefix;
	
	protected static $pathTemplates	= NULL;
	/**	@var		array		$loaded			List of loaded templates, used to avoid to load templates to often */
	protected static $loaded		= array();


	/**	@var		array		$plugins		List of template plugin instances */
	public static $plugins		= array();

	/**	@var		array		$plugins		List of template filter instances */
	public static $filters		= array();

	public static function getTemplatePath(){
		return self::$pathTemplates;
	}
	
	/**
	 *	Constructor
	 *	@access		public
	 *	@param		string		$fileName		File Name of Template File
	 *	@param		array		$elements		List of Elements {@link add()}
	 *	@return		void
	 */
	public function __construct( $fileName = NULL, $elements = NULL ){
		$this->elements		= array();
		$this->className	= get_class( $this );
		$this->fileName		= $fileName;
		$this->setTemplate( $fileName );
		$this->add( $elements ); 
	}
	
	/**
	 *	Adds an associative array with labels and elements to the template and returns number of added elements. 
	 *	@param		array 		Array where the <b>key</b> can be a string, integer or 
	 *							float and is the <b>label</b>. The <b>value</b> can be a 
	 *							string, integer, float or a template object and represents
	 *							the element to add.
	 *	@param		boolean		if TRUE an a tag is already used, it will overwrite it
	 *	@return		integer
	 */
	public function add( $elements, $overwrite = FALSE ){
		if( !is_array( $elements ) )
			return 0;
		$number	= 0;
		foreach( $elements as $key => $value ){
			if( !( is_string( $key ) || is_int( $key ) || is_float( $key ) ) )
				throw new InvalidArgumentException( 'Invalid key type "'.gettype( $key ).'"' );
			if( !strlen( trim( $key ) ) )
				throw new InvalidArgumentException( 'Key cannot be empty' );

			$isListObject	= $value instanceof ArrayObject || $value instanceof ADT_List_Dictionary;
			$isPrimitive	= is_string( $value ) || is_int( $value ) || is_float( $value ) || is_bool( $value );
			$isTemplate		= $value instanceof $this->className;
			if( is_null( $value ) )
				continue;
			else if( is_array( $value ) || $isListObject )
				$number	+= $this->addArrayRecursive( $key, $value, array(), $overwrite );
			else if( $isPrimitive || $isTemplate ){
//				if( $overwrite == TRUE )
//					$this->elements[$key] = array();
//				$this->elements[$key][] = $value;
				$this->elements[$key] = $value;
				$number	++;
			}
			else if( is_object( $value ) )
				$this->addObject( $key, $value, array(), $overwrite );
			else
				throw new InvalidArgumentException( 'Unsupported type '.gettype( $value ).' for "'.$key.'"' );
		}
		return $number;
	}

	public function addObject( $name, $object, $steps = array(), $overwrite = FALSE ){
		$number		= 0;
		$steps[]	= $name;
		$reflection	= new ReflectionObject( $object );
		foreach( $reflection->getProperties() as $property ){
			$key		= $property->getName();
			$methodName	= 'get'.ucFirst( $key );
			if( $property->isPublic() )
				$value	= $property->getValue( $object );
			else if( $reflection->hasMethod( $methodName ) )
				$value	= Alg_Object_MethodFactory::callObjectMethod( $object, $methodName );
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
	public function addArrayRecursive( $name, $data, $steps = array(), $overwrite = FALSE ){
		$number		= 0;
		$steps[]	= $name;
		foreach( $data as $key => $value ){
			$isListObject	= $value instanceof ArrayObject || $value instanceof ADT_List_Dictionary;
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
	 *	@param		string		$tag		Tag name
	 *	@param		string|integer|float|CMM_STE_Template
	 *	@param		boolean		if set to TRUE, it will overwrite an existing element with the same label
	 *	@return		void
	 */
	public function addElement( $tag, $element, $overwrite = FALSE ){
		$this->add( array( $tag => $element ), $overwrite );
	}

	/**
	 *	Registers a filter instance, which can be applied to all template tags.
	 *	If no keywords are given the filter's default keywords are registered.
	 *	You can use 'registerFilter' to avoid building an instance.
	 *	@access		public
	 *	@param		CMM_STE_Filter_Interface	$filter		Instance of filter
	 *	@param		array						$keywords	List of keywords to bind filter on
	 *	@return
	 */
	public static function addFilter( CMM_STE_Filter_Interface $filter, $keywords = array() ){
		if( !$keywords )
			$keywords	= $filter->getKeywords();
		foreach( $keywords as $keyword ){
			if( array_key_exists( $keyword, self::$filters ) )
				throw new Exception( 'Filter keyword "'.$keyword.'" is already taken by another filter' );
			self::$filters[$keyword]	= $filter;
		}
	}

	public static function addPlugin( CMM_STE_Plugin_Interface $plugin ){
		$keyword	= $plugin->getKeyword();
		$priority	= $plugin->getPriority();
		foreach( self::$plugins as $pluginsPriority => $plugins )
			foreach( $plugins as $pluginInstance )
				if( $pluginInstance instanceof CMM_STE_Plugin_Matrix )
					if( $pluginInstance->getKeyword() == $keyword )
						throw new Exception( 'Plugin keyword "'.$keyword.'" is already taken by another plugin' );
		if( !isset( self::$plugins[$priority] ) )
			self::$plugins[$priority]	= array();
		self::$plugins[$priority][]	= $plugin;
	}
	
	/**
	 *	Adds another Template.
	 *	@param		string		tagname
	 *	@param		string		template file
	 *	@param		array		array containing elements {@link add()}
	 *	@param		boolean		if set to TRUE, it will overwrite an existing element with the same label
	 *	@return		void
	 */
	public function addTemplate( $tag, $fileName, $element = NULL, $overwrite = FALSE ){
		$this->addElement( $tag, new self( $fileName, $element ), $overwrite );
	}

	/**
	 *	@todo		code doc
	 */
	protected function applyFilters( $matches ){
		$filters	= array();
		if( empty( $matches[3] ) )
			return $this->tmp;
		foreach( explode( '|', $matches[3] ) as $filter ){
			if( trim( $filter ) ){
				$parts		= explode( ':', trim( $filter ) );
				$filter		= $parts[0];
				$arguments	= isset( $parts[1] ) ? explode( ',', $parts[1] ) : array();
				if( array_key_exists( $filter, self::$filters ) ){
					if(	is_string( self::$filters[$filter] ) )
						self::$filters[$filter]	= Alg_Object_Factory::createObject( self::$filters[$filter] );
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
	protected function applyPlugins( &$content, $type = 'pre' ){
		ksort( self::$plugins );
		foreach( self::$plugins as $priority => $plugins )											//  iterate plugins priorities
			foreach( $plugins as $plugin )															//  iterate plugins in priority
				if( $plugin->getType() == $type )													//  plugin type is matching
					$content	= $plugin->work( $content, $this->elements );						//  apply plugin on template content
	}

	/**
	 *	Creates an output string from the templatefile where all labels will be replaced with apropriate elements.
	 *	If a non optional label wasn't specified, the method will throw a Template Exception
	 *	@access		public
	 *	@return		string
	 */
	public function create(){
		$out			= $this->template;															//  local copy of set template content
		$callbackFilter	= array( $this, 'applyFilters' );											//  create callback for applying filters on replacement of element labels
		
		$this->applyPlugins( $out, 'pre' );															//  apply pre processing plugins

		foreach( $this->elements as $label => $element ){											//  iterate over all registered element containers
		
			$tmp = '';																				//  
//			foreach( $labelElements as $element ){													//  iterate over all elements with current element container
	 			if( is_object( $element ) ){														//  element is an object
	 				if( !( $element instanceof $this->className ) )									//  object is not an template of this template engine
						continue;																	//  skip this one
					$element = $element->create();													//  render template before concat
	 			}
				$tmp	.= $element;
//			}
			$this->tmp	= $tmp;																		//  store current temporary element content for filters
			$pattern	= '/<%(\?)?('.$label.')(\|.+)?%>/';											//  create regular expression for element label with filter support
			$out		= preg_replace_callback( $pattern, $callbackFilter, $out );					//  realize placeholder, apply filters on content
 		}
		$out = preg_replace( '/<%\?.*%>/U', '', $out );    											//  remove left over optional placeholders
		$out = preg_replace( '/\n\s+\n/', "\n", $out );												//  remove double line breaks

		$this->applyPlugins( $out, 'post' );														//  apply post processing plugins

		$out = preg_replace( '/<%\??\w+\(.+\)%>/U', '', $out );    									//  remove left over plugin calls @todo kriss: handle this with exceptions later

		$tags = array();																			//  create container for left over placeholders
		if( preg_match_all( '/<%.*%>/U', $out, $tags ) === 0 )										//  no more placeholders left over
		    return $out; 																			//  return final result

		$tags	= array_shift( $tags );																//  
		foreach( $tags as $key => $value )															//  
			$tags[$key]	= preg_replace( '@(<%\??)|%>@', "", $value );								//  
		if( $this->fileName )																		//  
			throw new Exception_Template( Exception_Template::FILE_LABELS_MISSING, $this->fileName, $tags );
		throw new Exception_Template( Exception_Template::LABELS_MISSING, NULL, $tags );
	}

	/**
	 *	Returns all registered Elements.
	 *	@access		public
	 *	@return		array		all set elements
	 */
	public function getElements(){
		return $this->elements;
	}

	/**
	 *	Returns all marked elements from a comment.
	 *	@param		string		$comment		Comment Tag
	 *	@param		boolean		$unique			Flag: unique Keys only
	 *	@return		array						containing Elements or empty
	 */
	public function getElementsFromComment( $comment, $unique = TRUE ){
		$content = $this->getTaggedComment( $comment );
		if( !isset( $content ) )
			return NULL;

		$list	= array();
		$content = explode( "\n", $content );
		foreach( $content as $row ){
			if( preg_match( '/\s*@(\S+)?\s+(.*)/', $row, $out ) ){
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
	 *	@param		int			$type		Label Type: 0=all, 1=mandatory, 2=optional
	 *	@param		boolean		$xml		Flag: with or without delimiter
	 *	@return		array					Array of Labels
	 */
	public function getLabels( $type = 0, $xml = TRUE ){
 		$content = preg_replace( '/<%\??--.*--%>/sU', '', $this->template );	
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
	 *	@return		string					Comment or NULL
	 *	@todo		quote specialchars in tagname
	 */
	public function getTaggedComment( $tag, $xml = TRUE ){
		if( preg_match( '/<%--'.$tag.'(.*)--%>/sU', $this->template, $tag ) )
			return $xml ? $tag[0] : $tag[1];
		return NULL;
	}

	/**
	 *	Returns loaded Template.
	 *	@return		string		template content
	 */
	public function getTemplate(){
		return $this->template;
	}

	/**
	 *	Registers a filter, which can be applied to all template tags, by its class name.
	 *	@access		public
	 *	@param		string		$filter		Name of filter class, implementing CMM_STE_Filter_Interface
	 *	@param		array		$keywords	List of keywords to bind filter to
	 *	@return
	 */
	public static function registerFilter( $className, $keywords ){
		if( is_string( $keywords ) )
			$keywords	= array( $keywords );
		if( !is_array( $keywords ) )
			throw new InvalidArgumentException( 'Filter keywords must be an array of keywords or a string with a single keyword' );
		if( !$keywords )
			throw new InvalidArgumentException( 'No filter keywords given' );
		foreach( $keywords as $keyword ){
			if( array_key_exists( $keyword, self::$filters ) )
				throw new Exception( 'Filter keyword "'.$keyword.'" is already taken by another filter' );
			self::$filters[$keyword]	= $className;
		}
	}
	
	/**
	 *	Renders a Template with given Elements statically.
	 *	@access		public
	 *	@static
	 *	@param		string		$fileName		File Name of Template File
	 *	@param		array		$elements		List of Elements {@link add()}
	 *	@return		void
	 */
	public static function render( $fileName, $elements = array() ){
		$template	= new self( $fileName, $elements );
		return $template->create();
	}

	/**
	 *	Renders a Template String with given Elements statically.
	 *	@access		public
	 *	@static
	 *	@param		string		$string			Template String
	 *	@param		array		$elements		Map of Elements for Template String
	 *	@param		string		$fileName		File name of the template. Needed in case of error of missing labels
	 *	@return		string
	 */
	public static function renderString( $string, $elements = array(), $fileName = NULL ){
		$template				= new self();
		$template->template		= $string;
		$template->fileName		= $fileName;
		$template->add( $elements );
		return $template->create();
	}

	/**
	 *	Sets storage for caching.
	 *	@access		public
	 *	@static
	 *	@param		CMM_SEA_Adapter_Interface	$storage		Storage instance to be used for caching
	 *	@param		string						$prefix			Prefix for keys in cache.
	 *	@return		void
	 */
	public static function setCache( CMM_SEA_Adapter_Interface $storage, $prefix = '' )
	{
		self::$cache		= $storage;
		self::$cachePrefix	= $prefix;
	}

	/**
	 *	Loads a new template file if it exists. Otherwise it will throw an Exception.
	 *	@param		string		$fileName 	File Name of Template
	 *	@return		boolean
	 */
	public function setTemplate( $fileName ){
		if( empty( $fileName ) )																	//  no file name given
			return FALSE;																			//  return with negative result
			
		$filePath	= self::$pathTemplates.$fileName;												//  get file within set file path
		if( !in_array( $filePath, self::$loaded ) )													//  file not found in load list
			self::$loaded[$filePath] = 1;															//  append file to load list
		else
			self::$loaded[$filePath]++;																//  count file load
		if( self::$loaded[$filePath] > 100 )														//  file loaded 100 times
			throw new Exception( 'Template "'.$filePath.'" loaded too often' );						//  break because limit is reached

		$this->fileName	= $fileName;																//  set template file name, needed for exception handling
		$content	= self::$cache ? self::$cache->get( self::$cachePrefix.$filePath ) : NULL;		//  try to get file content from cache
		if( !$content ){																			//  no cached content
			if( !file_exists( $filePath ) )															//  file is not existing
				throw new Exception_Template( Exception_Template::FILE_NOT_FOUND, $filePath );		//  break with exception
			$content	= File_Reader::load( $filePath );											//  load file content
			if( self::$cache )																		//  cache is enabled
				self::$cache->set( self::$cachePrefix.$filePath, $content );						//  store file content in cache
		}
		$this->template = $content;																	//  set template content
		return TRUE;																				//  return with positive result
	}

	/**
	 *	Sets path to templates.
	 *	@access		public
	 *	@param		string		$path		Path to templates
	 *	@return		void
	 */
	public static function setTemplatePath( $path ){
		self::$pathTemplates	= preg_replace( "@(.+)/$@", "\\1/", $path );
	}
}
?>
