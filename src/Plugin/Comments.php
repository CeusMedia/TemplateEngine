<?php
declare(strict_types=1);

/**
 *	...
 *
 *	Copyright (c) 2011-2022 Christian Würker (ceusmedia.de)
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
 *	@copyright		2011-2022 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */

namespace CeusMedia\TemplateEngine\Plugin;

use CeusMedia\TemplateEngine\PluginAbstract;

/**
 *	...
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine_Plugin
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2022 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
class Comments extends PluginAbstract
{
	/**	@var		array		$options		Plugin options */
	protected array $options	= [
		'remove'	=> false,
	];

	/**	@var		string		$type			Plugin type (pre|post) */
	protected string $type		= 'post';

	/**
	 *	Apply plugin to template content.
	 *	@access		public
	 *	@param		string		$template		Template content
	 *	@param		array		$elements		Reference to elements assigned to template
	 *	@return		string
	 */
	public function work( string $template, array &$elements ): string
	{
 		$template	= (string) preg_replace( '/<%--.*--%>/sU', '', $template );						//  remove template engine style comments
 		if( isset( $this->options['remove'] ) && $this->options['remove'] )							//  HTML comments should be removed
			$template	= (string) preg_replace( '/<!--.+-->/sU', '', $template );					//  find and remove all HTML comments
		return $template;
	}
}
