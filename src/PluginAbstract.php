<?php
/**
 *	Abstraction for plugins.
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
 *	@package		CeusMedia_TemplateEngine
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceus-media.de>
 *	@copyright		2007-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
namespace CeusMedia\TemplateEngine;

/**
 *	Abstraction for plugins.
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceus-media.de>
 *	@copyright		2007-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
abstract class PluginAbstract implements PluginInterface
{
	/**	@var		string		$keyword		Plugin keyword */
	protected $keyword			= '';

	/**	@var		array		$options		Plugin options */
	protected $options			= array();

	/**	@var		integer		$priority		Plugin priority: 1(highest) - 9(lowest) */
	protected $priority			= 5;

	/**	@var		string		$type			Plugin type (pre|post) */
	protected $type				= 'pre';

	/**
	 *	Constructor.
	 *	Sets options.
	 *	@access		public
	 *	@param		array		$options		Plugin options to set above default plugin options
	 *	@return		void
	 */
	public function __construct( array $options = array() )
	{
		foreach( $options as $key => $value )
			$this->options[$key]	= $value;
		if( strlen( trim( $this->keyword ) ) === 0 )
			throw new \DomainException( 'Plugin class "'.get_class( $this ).'" does not set an unique plugin key' );
	}

	/**
	 *	Returns the keyword of this plugin instance.
	 *	@access		public
	 *	@return		string
	 */
	public function getKeyword(): string
	{
		return $this->keyword;
	}

	/**
	 *	Returns the options map of this plugin instance.
	 *	@access		public
	 *	@return		array
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 *	Returns the run order priority of this plugin instance.
	 *	@access		public
	 *	@return		integer
	 */
	public function getPriority(): int
	{
		return $this->priority;
	}

	/**
	 *	Returns the type (pre|post) of this plugin instance.
	 *	@access		public
	 *	@return		string
	 */
	public function getType(): string
	{
		return $this->type;
	}
}
