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
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine_Filter
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
namespace CeusMedia\TemplateEngine\Filter;
use CeusMedia\TemplateEngine\FilterAbstract;
use UI_HTML_Tag as HtmlTag;
use ADT_JSON_Formater as JsonFormater;

/**
 *	Filter to display code of several languages in several ways.
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine_Filter
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
class Code extends FilterAbstract
{
	/**	@var		array		$keywords		Keywords to bind filter to on register */
	protected $keywords	= [ 'code' ];

	/**
	 *	Apply filter to content.
	 *	@access		public
	 *	@param		string		$content		Content to apply filter on
	 *	@param		array		$arguments		Arguments for filter
	 *	@return		string
	 */
	public function apply( string $content, array $arguments = [] ): string
	{
		$format		= array_shift( $arguments );
		$language	= array_shift( $arguments );													//  get language from first argument
		switch( $format ){
			case 'xmp':
				$class		= $language ? $language : NULL;											//  get CSS class from chosen language
				$content	= HtmlTag::create( 'xmp', $content, [ 'class' => $class ] );
				break;
			case 'highlight':
				$content	= highlight_string( $content, TRUE );
				break;
			case 'json':
				$content	= JsonFormater::format( $content );
				break;
			default:
				$class		= $language ? $language : NULL;											//  get CSS class from chosen language
				$content	= HtmlTag::create( 'code', $content, [ 'class' => $class ] );		//  create code tag
				break;
		}
		return $content;
	}
}
