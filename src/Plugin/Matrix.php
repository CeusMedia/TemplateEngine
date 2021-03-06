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
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine_Plugin
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
namespace CeusMedia\TemplateEngine\Plugin;

/**
 *
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine_Plugin
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
class Matrix extends \CeusMedia\TemplateEngine\PluginAbstract
{
	/**	@var		string		$keyword		Plugin keyword */
	protected $keyword			= 'matrix';

	/**	@var		array		$options		Plugin options */
	protected $options			= array(
		'depth'		=> '1',
		'delimiter'	=> '.',
		'mode'		=> 'quite',
		'data'		=> array()
	);

	/**	@var		string		$type			Plugin type (pre|post) */
	protected $type				= 'pre';

	/**
	 *	Constructor.
	 *	Sets options.
	 *	@access		public
	 *	@param		array		$options		Plugin options to set above default plugin options
	 *	@return		void
	 *	@throws		\Exception					if options do not contain data of matrix
	 */
	public function __construct( array $options = array() )
	{
		if( isset( $options['keyword'] ) ){
			$this->keyword	= $options['keyword'];
			unset( $options['keyword'] );
		}
		if( isset( $options['data'] ) && is_array( $options['data'] ) )
			$this->options['data']	=& $options['data'];
		if( !isset( $this->options['data'] ) )
			throw new \Exception( 'No matrix data provided' );
		parent::__construct( $options );
	}

	/**
	 *	Apply plugin to template content.
	 *	@access		public
	 *	@param		string		$template		Template content
	 *	@param		array		$elements		Reference to elements assigned to template
	 *	@return		string
	 */
	public function work( string $template, array &$elements ): string
	{
		$matches	= array();
		$pattern	= '/<(\?)?%'.$this->keyword.'\((.+)\)%>/U';
		preg_match_all( $pattern, $template, $matches );
		if( !$matches[0] )
			return $template;
		$value	= NULL;
		for( $i=0; $i<count( $matches[0] ); $i++ )
			if( $this->extractValue( $matches[2][$i], $value ) )
				$template	= str_replace( $matches[0][$i], $value, $template );
		return $template;
	}

	/**
	 *	Tries to get a value by matrix.
	 *	@access		public
	 *	@param		string		$key		Matrix key
	 *	@param		mixed		$value		Reference to value for matrix key (if existing)
	 *	@return		boolean
	 */
	protected function extractValue( string $key, &$value )
	{
		$depth	= $this->options['depth'];
		$parts	= explode( $this->options['delimiter'], $key );
		if( count( $parts ) != $depth )
			throw new \InvalidArgumentException( 'Depth ('.count( $parts ).') of key ('.$key.') does not match matrix depth ('.$depth.')' );
		$data	= $this->options['data'];
		for( $i=0; $i<$depth; $i++ ){
			if( !isset( $data[$parts[$i]] ) ){
				if( $this->options['mode'] == 'strict' )
					throw new \Exception( 'Invalid: '.implode( '.', array_slice( $parts, 0, $i + 1) ) );
				else if( $this->options['mode'] == 'verbose' )
					$data	= 'Missing: '.$key;
				else
					$data	= '';
				break;
			}
			$data	= $data[$parts[$i]];
		}
		$value	= $data;
		return TRUE;
	}
}
