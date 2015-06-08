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
 *	@category		cmModules
 *	@package		STE.Plugin
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			15.09.2011
 *	@version		$Id$
 */
namespace CeusMedia\TemplateEngine;
/**
 *	Abstraction for plugins.
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
abstract class PluginAbstract implements \CeusMedia\TemplateEngine\PluginInterface{
	
	/**	@var		string		$keyword		Plugin keyword */
	protected $keyword			= NULL;

	/**	@var		array		$options		Plugin options */
	protected $options = array();

	/**	@var		string		$priority		Plugin priority: 1(highest) - 9(lowest) */
	protected $priority			= '5';

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
		if( $options && is_array( $options ) )
			foreach( $options as $key => $value )
				$this->options[$key]	= $value;
	}

	/**
	 *	Returns the keyword of this plugin instance.
	 *	@access		public
	 *	@return		string
	 */
	public function getKeyword(){
		return $this->keyword;
	}

	/**
	 *	Returns the options map of this plugin instance.
	 *	@access		public
	 *	@return		array
	 */
	public function getOptions(){
		return $this->options;
	}

	/**
	 *	Returns the run order priority of this plugin instance.
	 *	@access		public
	 *	@return		integer
	 */
	public function getPriority(){
		return $this->priority;
	}

	/**
	 *	Returns the type (pre|post) of this plugin instance.
	 *	@access		public
	 *	@return		string
	 */
	public function getType(){
		return $this->type;
	}
}
?>
