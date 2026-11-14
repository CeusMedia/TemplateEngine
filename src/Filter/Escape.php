<?php
declare(strict_types=1);

/**
 *	Filter to escape HTML entities in content.
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
 *	@package		CeusMedia_TemplateEngine_Filter
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2024 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
namespace CeusMedia\TemplateEngine\Filter;

use CeusMedia\TemplateEngine\FilterAbstract;

/**
 *	Filter to escape HTML entities in content.
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine_Filter
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2024 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
class Escape extends FilterAbstract
{
	/**	@var		array<string>	$keywords		Keywords to bind filter to on register */
	protected array $keywords		= ['escape', 'htmlentities'];

	/**
	 *	Apply filter to content.
	 *	@access		public
	 *	@param		string		$content		Content to apply filter on
	 *	@param		array		$arguments		Arguments for filter (optional encoding and double_encode flag)
	 *	@return		string
	 */
	public function apply( string $content, array $arguments = [] ): string
	{
		$shifted	= array_shift( $arguments );
		$encoding	= NULL !== $shifted ? (string) $shifted : 'UTF-8';
		$flags		= ENT_QUOTES;
		$doubleEncode	= TRUE;
		if( [] !== $arguments )
			$doubleEncode	= (bool) array_shift( $arguments );
		return htmlentities( $content, $flags, $encoding, $doubleEncode );
	}
}
