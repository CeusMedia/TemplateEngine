<?php
declare(strict_types=1);

/**
 *	...
 *
 *	Copyright (c) 2011-2023 Christian Würker (ceusmedia.de)
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
 *	@copyright		2011-2023 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */

namespace CeusMedia\TemplateEngine\Plugin;

use CeusMedia\TemplateEngine\PluginAbstract;
use RuntimeException;

/**
 *	...
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine_Plugin
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2023 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
class Tidy extends PluginAbstract
{
	/**	@var		array		$options		Plugin options */
	protected array $options			= [
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
	];

	/**	@var		integer		$priority		Plugin priority: 1(highest) - 9(lowest) */
	protected int $priority			= 9;

	/**	@var		string		$type			Plugin type (pre|post) */
	protected string $type				= 'post';

	/**
	 *	Apply plugin to template content.
	 *	@access		public
	 *	@param		string		$template		Template content
	 *	@param		array		$elements		Reference to elements assigned to template
	 *	@return		string
	 *	@throws		RuntimeException			if extension 'tidy' is not installed
	 */
	public function work( string $template, array &$elements ): string
	{
		if( !extension_loaded( 'tidy' ) )
			throw new RuntimeException( 'tidy extension not loaded' );
		$tidy = new \tidy;
//    	$c	 = new Alg_Time_Clock();
		$template	= $tidy->repairString( $template, $this->options, 'utf8' );
//		remark( $c->stop() );
		return $template;
	}
}
