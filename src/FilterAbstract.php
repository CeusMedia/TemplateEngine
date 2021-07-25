<?php
/**
 *	Abstraction for template engine filters.
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
use CeusMedia\TemplateEngine\FilterInterface;

/**
 *	Abstraction for template engine filters.
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceus-media.de>
 *	@copyright		2007-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
abstract class FilterAbstract implements FilterInterface
{

	/**	@var		array		$keywords		Keywords to bind filter to on register */
	protected $keywords	= [];

	/**	@var		array		$options		Filter options */
	protected $options	= [];

	/**
	 *	Constructor.
	 *	Sets options.
	 *	@access		public
	 *	@param		array		$options		Filter options to set above default filter options
	 *	@return		void
	 */
	public function __construct( array $options = array() )
	{
		foreach( $options as $key => $value )
			$this->options[$key]	= $value;
	}

	/**
	 *	Returns a list of keywords of this filter.
	 *	@access		public
	 *	@return		array
	 */
	public function getKeywords(): array
	{
		return $this->keywords;
	}

	/**
	 *	Sets keywords for this filter.
	 *	@access		public
	 *	@param		array		$keywords		List of filter keywords
	 *	@param		boolean		$append			Flag: append keywords, otherwise replace
	 *	@return		FilterAbstract
	 */
	public function setKeywords( array $keywords, bool $append = FALSE ): FilterAbstract
	{
		if( $append ){
			foreach( $keywords as $keyword )
				$this->keywords[]	= $keyword;
		}
		else
			$this->keywords	= $keywords;
		return $this;
	}
}
