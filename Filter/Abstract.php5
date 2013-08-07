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
 *	@category		cmModules
 *	@package		STE.Filter
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			15.09.2011
 *	@version		$Id$
 */
/**
 *	Abstraction for template engine filters.
 *	@category		cmModules
 *	@package		STE.Filter
 *	@implements		CMM_STE_Filter_Interface
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			15.09.2011
 *	@version		$Id$
 */
abstract class CMM_STE_Filter_Abstract implements CMM_STE_Filter_Interface{

	/**	@var		array		$keywords		Keywords to bind filter to on register */
	protected $keywords	= array();
	
	/**	@var		array		$options		Filter options */
	protected $options	= array();

	/**
	 *	Constructor.
	 *	Sets options.
	 *	@access		public
	 *	@param		array		$options		Filter options to set above default filter options
	 *	@return		void
	 */
	public function __construct( $options = NULL ){
		if( $options && is_array( $options ) )
			foreach( $options as $key => $value )
				$this->options[$key]	= $value;
	}

	/**
	 *	Returns a list of keywords of this filter.
	 *	@access		public
	 *	@return		array
	 */
	public function getKeywords(){
		return $this->keywords;
	}

	/**
	 *	Sets keywords for this filter.
	 *	@access		public
	 *	@param		array		$keywords		List of filter keywords
	 *	@param		boolean		$append			Flag: append keywords, otherwise replace
	 *	@return		void
	 */
	public function setKeywords( $keywords, $append = FALSE ){
		if( !is_array( $keywords ) )
			throw new InvalidArgumentException( 'Keywords must be of array' );
		if( $append )
			foreach( $keywords as $keyword )
				$this->keywords[]	= $keyword;
		else
			$this->keywords	= $keywords;
	}
}
?>