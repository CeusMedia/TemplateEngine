<?php
declare(strict_types=1);

/**
 *	Mock PSR-16 cache implementation for testing.
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
 *	@package		CeusMedia_TemplateEngine_Test
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2011-2024 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/TemplateEngine
 */
namespace CeusMedia\TemplateEngineTest;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class MockCache implements CacheInterface
{
	/** @var array<string, mixed> $store */
	private array $store	= [];

	/**
	 * @var array<string>
	 */
	private array $keys	= [];

	/**
	 * @param string $key
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function get( string $key, mixed $default = NULL ): mixed
	{
		return $this->store[$key] ?? $default;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param null|int|\DateInterval $ttl
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function set( string $key, mixed $value, null|int|\DateInterval $ttl = NULL ): bool
	{
		$this->store[$key]	= $value;
		$this->keys[]		= $key;
		return TRUE;
	}

	/**
	 * @param string $key
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function delete( string $key ): bool
	{
		unset( $this->store[$key ] );
		return TRUE;
	}

	/**
	 * @param array<string> $keys
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function clear(): bool
	{
		$this->store	= [];
		$this->keys		= [];
		return TRUE;
	}

	/**
	 * @param iterable<string, mixed> $values
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function setMultiple( iterable $values, null|int|\DateInterval $ttl = NULL ): bool
	{
		foreach( $values as $key => $value )
			$this->set( (string) $key, $value );
		return TRUE;
	}

	/**
	 * @param iterable<string> $keys
	 * @param mixed $default
	 * @return array<string, mixed>
	 * @throws InvalidArgumentException
	 */
	public function getMultiple( iterable $keys, mixed $default = NULL ): iterable
	{
		$result	= [];
		foreach( $keys as $key )
			$result[$key]	= $this->get( $key, $default );
		return $result;
	}

	/**
	 * @param iterable<string> $keys
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function deleteMultiple( iterable $keys ): bool
	{
		foreach( $keys as $key )
			$this->delete( $key );
		return TRUE;
	}

	/**
	 * @param string $key
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function has( string $key ): bool
	{
		return isset( $this->store[$key] );
	}

	/**
	 * Returns the list of keys that have been stored.
	 * @return array<string>
	 */
	public function getStoredKeys(): array
	{
		return $this->keys;
	}
}
