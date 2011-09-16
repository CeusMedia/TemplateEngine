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
class CMM_STE_Plugin_Tidy extends CMM_STE_Plugin_Abstract {
	
	/**	@var		array		$options		Plugin options */
	protected $options			= array(
        'show-body-only'				=> true,
        'clean'							=> true,
        'char-encoding'					=> 'utf8',
        'output-html'					=> false,
        'output-xml'					=> false,
        'output-xhtml'					=> true,
        'numeric-entities'				=> true,
        'ascii-chars'					=> false,
        'doctype'						=> 'strict',
        'bare'							=> true,
        'fix-uri'						=> true,
        'indent'						=> true,
        'indent-spaces'					=> 2,
        'tab-size'						=> 4,
        'wrap-attributes'				=> true,
        'wrap'							=> 0,
        'indent-attributes'				=> false,
        'join-classes'					=> false,
        'join-styles'					=> false,
        'enclose-block-text'			=> true,
        'fix-bad-comments'				=> true,
        'fix-backslash'					=> true,
        'replace-color'					=> false,
        'wrap-jste'						=> false,
        'wrap-php'						=> false,
        'write-back'					=> true,
        'drop-proprietary-attributes'	=> false,
        'hide-comments'					=> true,
        'hide-endtags'					=> false,
        'literal-attributes'			=> false,
        'drop-empty-paras'				=> true,
        'enclose-text'					=> true,
        'quote-ampersand'				=> true,
        'quote-marks'					=> false,
        'quote-nbsp'					=> true,
        'vertical-space'				=> false,
        'wrap-script-literals'			=> false,
        'tidy-mark'						=> true,
        'merge-divs'					=> false,
        'repeated-attributes'			=> 'keep-last',
        'break-before-br'				=> true,
	);

	/**	@var		string		$priority		Plugin priority: 1(highest) - 9(lowest) */
	protected $priority			= 9;

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
		if( !extension_loaded( 'tidy' ) )
			throw new RuntimeException( 'tidy extension not loaded' );
    	$tidy = new tidy;
//    	$c	 = new Alg_Time_Clock();
		$template	= $tidy->repairString( $template, $this->options, 'utf8' );
//		remark( $c->stop() );
		return $template;
	}
}
?>