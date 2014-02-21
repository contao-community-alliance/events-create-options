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

use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Controller\GetTemplateGroupEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 */
class CreateOptionsEventCallbackFactory
{
	/**
	 * Create a new event driven options callback.
	 *
	 * @param string         $eventName      The event name to dispatch.
	 * @param string|closure $classOrFactory Class name of the event or a factory method to create the event object.
	 *
	 * @return array Return a Contao callback that can be used as options_callback.
	 */
	static public function createCallback($eventName, $classOrFactory = null)
	{
		$callback = function ($dc) use ($eventName, $classOrFactory) {
			if (!$classOrFactory) {
				$event = new CreateOptionsEvent($dc);
			}
			else if (is_callable($classOrFactory)) {
				$event = call_user_func($classOrFactory, $dc);
			}
			else {
				/** @var CreateOptionsEvent $event */
				$event = new $classOrFactory($dc);
			}

			/** @var EventDispatcher $eventDispatcher */
			$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
			$eventDispatcher->dispatch($eventName, $event);

			return $event->getOptions()
				->getArrayCopy();
		};

		if (version_compare(VERSION, '3.2', '<')) {
			return CreateOptionsEventCallbackHelper::registerEventCallback($callback);
		}
		else {
			return $callback;
		}
	}

	/**
	 * Create a new options callback, that calls any callable.
	 *
	 * @param callable $callable
	 *
	 * @return array Return a Contao callback that can be used as options_callback.
	 */
	static public function createCallableCallback($callable)
	{
		$callback = function ($dc) use ($callable) {
			return call_user_func($callable, $dc);
		};

		if (version_compare(VERSION, '3.2', '<')) {
			return CreateOptionsEventCallbackHelper::registerEventCallback($callback);
		}
		else {
			return $callback;
		}
	}

	/**
	 * Convenience method to generate a "getTemplateGroup" callback.
	 *
	 * @param string $prefix
	 *
	 * @return array Return a Contao callback that can be used as options_callback.
	 */
	static public function createTemplateGroupCallback($prefix)
	{
		$callable = function() use ($prefix) {
			$event = new GetTemplateGroupEvent($prefix);

			/** @var EventDispatcher $eventDispatcher */
			$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
			$eventDispatcher->dispatch(ContaoEvents::CONTROLLER_GET_TEMPLATE_GROUP, $event);

			return $event->getTemplates()->getArrayCopy();
		};

		return static::createCallableCallback($callable);
	}
}
