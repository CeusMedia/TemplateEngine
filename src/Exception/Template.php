<?php
declare(strict_types=1);

/**
 *	Exception for Templates.
 *
 *	Copyright (c) 2007-2024 Christian Würker (ceusmedia.de)
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
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2024 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */

namespace CeusMedia\TemplateEngine\Exception;

use DomainException;
use RuntimeException;
use Throwable;

/**
 *	Exception for Templates.
 *	@category		Library
 *	@package		CeusMedia_TemplateEngine
 *	@author			David Seebacher <dseebacher@gmail.com>
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2024 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
class Template extends RuntimeException
{
	public const FILE_NOT_FOUND			= 0;
	public const FILE_LABELS_MISSING	= 1;
	public const LABELS_MISSING			= 2;
	public const FILE_LOAD_LIMIT		= 3;

	/**	@var		array<int,string>	$messages		Map of Exception Messages, can be overwritten statically */
	public static array $messages	= [
		self::FILE_NOT_FOUND		=> 'Template File "%1$s" is missing',
		self::FILE_LABELS_MISSING	=> 'Template "%1$s" is missing %2$s',
		self::LABELS_MISSING		=> 'Template is missing %1$s',
		self::FILE_LOAD_LIMIT		=> 'Load limit reached for template "%1$s"'
	];

	/**	@var		array<int,string>	$labels			Holds all not used and non-optional labels */
	protected array $labels			= [];

	/**	@var		string|NULL			$filePath		File Path of Template, set only if not found */
	protected ?string $filePath			= NULL;

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		int				$code			Exception Code
	 *	@param		string|NULL		$fileName		File Name of Template
	 *	@param		array			$data			Some additional data
	 *	@param		Throwable|NULL	$previous		Parent exception
	 *	@return		void
	 */
	public function __construct( $code, ?string $fileName = NULL, array $data = [], ?Throwable $previous = NULL )
	{
		$this->filePath	= $fileName;
		$this->labels	= $data;
		$tagList	= '"'.implode( '", "', $data ).'"';
		switch( $code ){
			case self::FILE_NOT_FOUND:
			case self::FILE_LOAD_LIMIT:
				$message		= sprintf( self::$messages[$code], $fileName );
				break;
			case self::FILE_LABELS_MISSING:
				$message		= sprintf( self::$messages[self::FILE_LABELS_MISSING], $fileName, $tagList );
				break;
			case self::LABELS_MISSING:
				$message		= sprintf( self::$messages[self::LABELS_MISSING], $tagList );
				break;
			default:
				throw new DomainException( 'Invalid template exception code' );
		}
		parent::__construct( $message, $code, $previous );
	}

	/**
	 *	Returns File Path of Template if not found.
	 *	@access		public
	 *	@return		string|NULL		{@link $filePath}
	 */
	public function getFilePath(): ?string
	{
		return $this->filePath;
	}

	/**
	 *	Returns not used Labels.
	 *	@access		public
	 *	@return		array		{@link $labels}
	 */
	public function getNotUsedLabels(): array
	{
		return $this->labels;
	}
}
