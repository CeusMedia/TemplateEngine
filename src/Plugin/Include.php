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
class Include extends \CeusMedia\TemplateEngine\PluginAbstract{
	
	/**	@var		string		$keyword		Plugin keyword */
	protected $keyword			= 'include';
	
	/**	@var		string		$type			Plugin type (pre|post) */
	protected $type				= 'pre';
	
	/**
	 *	Apply plugin to template content.
	 *	@access		public
	 *	@param		string		$template		Template content
	 *	@param		array		$elements		Reference to elements assigned to template
	 *	@return		string
	 */
	public function work( $template, &$elements ){
		$matches	= array();
		$pattern	= '/<(\?)?%'.$this->keyword.'\((.+)\)(\|.+)?%>/U';
		preg_match_all( $pattern, $template, $matches );
		if( !$matches[0] )
			return $template;

		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$hash		= 'STE'.uniqid();															//  insert a hash value as replacement
			$filePath	= $matches[2][$i];
			$content	= \CeusMedia\TemplateEngine\Template::render( $filePath, $elements );
			$elements[$hash]	= $content;
			$value		= '<%?'.$hash.$matches[3][$i].'%>';
			$template	= str_replace( $matches[0][$i], $value, $template );
		}
		return $template;
	}
}
?>
