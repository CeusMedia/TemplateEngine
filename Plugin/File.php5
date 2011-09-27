<?php
/**
 *	
 *
 *	Copyright (c) 2011 Christian Würker (ceusmedia.de)
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
 *	@category		cmModules
 *	@package		STE.Plugin
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			15.09.2011
 *	@version		$Id$
 */
/**
 *	
 *	@category		cmModules
 *	@package		STE.Plugin
 *	@implements		CMM_STE_Plugin_Interface
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			15.09.2011
 *	@version		$Id$
 */
class CMM_STE_Plugin_File extends CMM_STE_Plugin_Abstract{

	/**	@var		string		$keyword		Plugin keyword */
	protected $keyword			= 'file';
	
	/**	@var		array		$options		Plugin options */
	protected $options			= array(
		'path'		=> '',
		'mode'		=> 'strict'
	);

	/**	@var		string		$type			Plugin type (pre|post) */
	protected $type				= 'pre';

	/**
	 *	Constructor.
	 *	Sets options.
	 *	@access		public
	 *	@param		array		$options		Plugin options to set above default plugin options
	 *	@return		void
	 */
	public function __construct( $options = NULL ){
		if( isset( $options['keyword'] ) ){
			$this->keyword	= $options['keyword'];
			unset( $options['keyword'] );
		}
		if( !isset( $options['path'] ) )
			throw new InvalidArgumentException( 'No path set' );
		parent::__construct( $options );
	}
	
	/**
	 *	Apply plugin to template content.
	 *	@access		public
	 *	@param		string		$template		Template content
	 *	@param		array		$elements		Elements assigned to template
	 *	@return		string
	 */
	public function work( $template, &$elements ){
		if( empty( $this->options['path'] ) )
			throw new RuntimeException( 'No path set' );
		$matches	= array();
		$pattern	= '/<(\?)?%'.$this->keyword.'\((.+)\)(|.+)?%>/U';
		preg_match_all( $pattern, $template, $matches );
		if( !$matches[0] )
			return $template;
		$value	= NULL;
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			try{
				$hash		= 'STE'.uniqid();														//  insert a hash value as replacement
				$content	= File_Reader::load( $this->options['path'].$this->getFileNameFromKey( $matches[2][$i] ) );			//  load file content
				$content	= preg_replace( '/<%(.+)%>/U', '&lt;%$1%&gt;', $content );				//  
				$elements[$hash]	= $content;													//  store file content in elements by its hash value as new template tag
				$value		= '<%?'.$hash.$matches[3][$i].'%>';														
			}
			catch( Exception $e ){
				if( $this->options['mode'] == 'strict' )
					throw new Exception_IO( 'Invalid file', 0, $matches[2][$i], $e );
				else if( $this->options['mode'] == 'verbose' )
					$value	= 'Missing file: '.$matches[2][$i];
				else
					$value	= '';
			}
			$template	= str_replace( $matches[0][$i], $value, $template );
		}
		return $template;
	}
	
	/**
	 *	Returnes the file name by its key,
	 *	This method is meant to be overriden for different behaviour.
	 *	@access		protected
	 *	@param		string		$key			Key of external file to load
	 *	@return		string
	 */
	protected function getFileNameFromKey( $key )
	{
		return $key;
	}
}
?>
