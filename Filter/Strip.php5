<?php
/**
 *	Filter to strip tags etc.
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
 *	Filter to strip tags etc.
 *	@category		cmModules
 *	@package		STE.Filter
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			22.09.2011
 *	@version		$Id$
 */
class CMM_STE_Filter_Strip extends CMM_STE_Filter_Abstract{

	/**	@var		array		$keywords		Keywords to bind filter to on register */
	protected $keywords	= array( 'strip' );

	
	/**
	 *	Apply filter to content.
	 *	@access		public
	 *	@param		string		$content		Content to apply filter on
	 *	@param		string		$arguments		Arguments for filter, a list of: tags | space | comments | styles | scripts
	 *	@return		string
	 */
	public function apply( $content, $arguments = array() ){
		foreach( $arguments as $type ){
			switch( $type ){
				case 'tags':
					$content	= strip_tags( $content );
					break;
				case 'space':
					$content	= trim( $content );
					break;
				case 'comments':
					$content	= Alg_Text_Filter::stripComments( $content );
					break;
				case 'styles':
					$content	= Alg_Text_Filter::stripStyles( $content );
					break;
				case 'scripts':
					$content	= Alg_Text_Filter::stripScripts( $content );
					break;
			}
		}
		return $content;
	}
}
?>