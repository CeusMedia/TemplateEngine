<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

/**
 *	...
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

namespace CeusMedia\TemplateEngine\Plugin;

use CeusMedia\TemplateEngine\PluginAbstract;
use CeusMedia\Common\FS\File\Reader as FileReader;
use CeusMedia\Common\Exception\IO as IoException;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 *	...
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine_Plugin
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2024 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
class File extends PluginAbstract
{
	/**	@var		string		$keyword		Plugin keyword */
	protected string $keyword	= 'file';

	/**	@var		array		$options		Plugin options */
	protected array $options	= [
		'path'		=> '',
		'mode'		=> 'strict'
	];

	/**	@var		string		$type			Plugin type (pre|post) */
	protected string $type		= 'pre';

	/**
	 *	Constructor.
	 *	Sets options.
	 *	@access		public
	 *	@param		array		$options		Plugin options to set above default plugin options
	 *	@return		void
	 */
	public function __construct( array $options = [] )
	{
		if( isset( $options['keyword'] ) ){
			$this->keyword	= $options['keyword'];
			unset( $options['keyword'] );
		}
		if( isset( $options['path'] ) ){
			$this->options['path']	= $options['path'];
			unset( $options['path'] );
		}
		if( !isset( $this->options['path'] ) )
			throw new InvalidArgumentException( 'No path set' );
		parent::__construct( $options );
	}

	/**
	 *	Apply plugin to template content.
	 *	@access		public
	 *	@param		string		$template		Template content
	 *	@param		array		$elements		Elements assigned to template
	 *	@return		string
	 *	@throws		RuntimeException
	 *	@throws		IoException
	 */
	public function work( string $template, array &$elements ): string
	{
		if( !isset( $this->options['path'] ) )
			throw new RuntimeException( 'No path set' );
		$matches	= [];
		$pattern	= '/<(\?)?%'.$this->keyword.'\((.+)\)(\|.+)?%>/U';
		preg_match_all( $pattern, $template, $matches );
		if( '' === ( $matches[0] ?? '' ) )
			return $template;

		for( $i=0; $i<count( $matches[0] ); $i++ ){
			try{
				$hash		= 'STE'.uniqid();														//  create unique hash value
				/** @var string $content */
				$content	= FileReader::load( $this->getFileNameFromKey( $matches[2][$i] ) );		//  load file content
				$content	= preg_replace( '/<%(.+)%>/U', '&lt;%$1%&gt;', $content );				//  escape tags in content
				$elements[$hash]	= $content;														//  store file content in elements by its hash value as new template tag
				$value		= '<%?'.$hash.$matches[3][$i].'%>';										//  replacement for tag is a hashtag
			}
			catch( Throwable $e ){																	//  catch all exceptions
				if( 'strict' === $this->options['mode'] )											//  strict error mode
					throw new IoException( 'Invalid file', 0, $e, $matches[2][$i] );				//  throw an exception
				else if( 'verbose' === $this->options['mode'] )										//  verbose error mode
					$value	= 'Missing file: '.$matches[2][$i];										//
				else
					$value	= '';
			}
			$template	= str_replace( $matches[0][$i], $value, $template );						//  replace tag by hash value, content will be inserted later in template engine itself
		}
		return $template;
	}

	/**
	 *	Returns the file name by its key,
	 *	This method is meant to be overridden for different behaviour.
	 *	@access		protected
	 *	@param		string		$key			Key of external file to load
	 *	@return		string
	 */
	protected function getFileNameFromKey( string $key ): string
	{
		return $this->options['path'].$key;
	}
}
