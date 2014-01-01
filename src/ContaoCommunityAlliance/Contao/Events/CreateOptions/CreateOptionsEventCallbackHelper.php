<?php

/**
 * Create options event for Contao Open Source CMS
 * Copyright (C) 2013, 2014 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  (c) 2013, 2014 Contao Community Alliance
 * @author         Tristan Lins <tristan.lins@bit3.de>
 * @package        events-create-options
 * @license        LGPL
 * @filesource
 */

namespace ContaoCommunityAlliance\Contao\Events\CreateOptions;

/**
 *
 */
class CreateOptionsEventCallbackHelper
{
	static protected $instance;

	static public function getInstance()
	{
		if (static::$instance === null) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	static protected $eventNames;

	static public function registerEventCallback($callback)
	{
		do {
			$methodName = uniqid('func_');
		}
		while (isset(static::$eventNames[$methodName]));

		static::$eventNames[$methodName] = $callback;

		return array(
			'ContaoCommunityAlliance\Contao\EventDispatcher\Helper\CreateOptionsEventCallbackHelper',
			static::$eventNames[$methodName]
		);
	}

	/**
	 * @param string $methodName
	 * @param array  $args
	 */
	public function __call($methodName, array $args)
	{
		if (isset(static::$eventNames[$methodName])) {
			$callback = static::$eventNames[$methodName];
			return call_user_func_array($callback, $args);
		}

		throw new \RuntimeException('No create options event callback found for ' . $methodName);
	}
}
