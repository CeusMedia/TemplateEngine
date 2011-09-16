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
class CMM_STE_Plugin_Include extends CMM_STE_Plugin_Abstract implements CMM_STE_Plugin_Interface {
	
	/**	@var		string		$keyword		Plugin keyword */
	protected $keyword			= 'include';
	
	/**	@var		string		$type			Plugin type (pre|post) */
	protected $type				= 'post';
	
	/**
	 *	Apply plugin to template content.
	 *	@access		public
	 *	@param		string		$template		Template content
	 *	@param		array		$elements		Elements assigned to template
	 *	@return		string
	 */
	public function work( $template, $elements ){
		$matches	= array();
		$pattern	= '/<(\?)?%'.$this->keyword.'\((.+)\)%>/U';
		preg_match_all( $pattern, $template, $matches );
		if( !$matches[0] )
			return $template;

		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$filePath	= CMM_STE_Template::getTemplatePath().$matches[2][$i];
			$nested		= CMM_STE_Template::render( $filePath, $elements );
			$template	= str_replace( $matches[0][$i], $nested, $template );
		}
		return $template;
	}
}
?>