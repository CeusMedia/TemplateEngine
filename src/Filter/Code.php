<?php
/**
 *	Filter to display code of several languages in several ways.
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
 *	@package		STE.Filter
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			22.09.2011
 *	@version		$Id$
 */
/**
 *	Filter to display code of several languages in several ways.
 *	@category		cmModules
 *	@package		STE.Filter
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			22.09.2011
 *	@version		$Id$
 */
class CMM_STE_Filter_Code extends CMM_STE_Filter_Abstract{

	/**	@var		array		$keywords		Keywords to bind filter to on register */
	protected $keywords	= array( 'code' );

	/**
	 *	Apply filter to content.
	 *	@access		public
	 *	@param		string		$content		Content to apply filter on
	 *	@param		string		$arguments		Arguments for filter
	 *	@return		string
	 */
	public function apply( $content, $arguments = array()){
		$format		= array_shift( $arguments );
		$language	= array_shift( $arguments );													//  get language from first argument
		switch( $format ){
			case 'xmp':
				$class		= $language ? $language : NULL;											//  get CSS class from chosen language
				$content	= UI_HTML_Tag::create( 'xmp', $content, array( 'class' => $class ) );
				break;
			case 'highlight':
				$content	= highlight_string( $content, TRUE );
				break;
			case 'json':
				$content	= ADT_JSON_Formater::format( $content );
				break;
			default:
				$class		= $language ? $language : NULL;											//  get CSS class from chosen language
				$content	= UI_HTML_Tag::create( 'code', $content, array( 'class' => $class ) );	//  create code tag
				break;
		}
		return $content;
	}
}
?>