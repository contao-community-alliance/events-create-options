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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 */
class CreateOptionsEventHelper
{
	/**
	 * Directly invoke the create-options event.
	 * In most cases you will not use this, but when you need to bypass the options_callback (like when you use
	 * DC_General and MultiColumnWizard), you can use this helper directly.
	 *
	 * @param \DataContainer  $dc             The data container.
	 * @param string          $eventName      The event name to dispatch.
	 * @param string|\Closure $classOrFactory Class name of the event or a factory method to create the event object.
	 *
	 * @return array Return a Contao callback that can be used as options_callback.
	 *
	 * @api
	 */
	static public function invoke($dc, $eventName, $classOrFactory = null)
	{
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
	}
}
