<?php
/**
 *	Interface for template engine filters.
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
 *	Interface for template engine filters.
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceus-media.de>
 *	@copyright		2007-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
interface FilterInterface
{
	/**
	 *	Constructor.
	 *	Sets options.
	 *	@access		public
	 *	@param		array		$options		Filter options to set above default filter options
	 *	@return		void
	 */
	public function __construct( array $options = [] );

	/**
	 *	Apply filter to content.
	 *	@access		public
	 *	@param		string		$content		Content to apply filter on
	 *	@param		array		$arguments		Arguments for filter
	 *	@return		string
	 */
	public function apply( string $content, array $arguments = [] ): string;

	/**
	 *	Returns a list of keywords of this filter.
	 *	@access		public
	 *	@return		array
	 */
	public function getKeywords(): array;

	/**
	 *	Sets keywords for this filter.
	 *	@access		public
	 *	@param		array		$keywords		List of filter keywords
	 *	@param		boolean		$append			Flag: append keywords, otherwise replace
	 *	@return		FilterAbstract
	 */
	public function setKeywords( array $keywords, bool $append = FALSE ): FilterAbstract;
}
