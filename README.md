# Events: Create options

Event and helper classes to provide option_callback's via events.

In your DCA, define the `options_callback` with the factory class `CreateOptionsEventCallbackFactory`.

```php
use ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory;

$GLOBALS['TL_DCA']['tl_foo']['fields']['some_select'] = array(
	'inputType' => 'select',
	...
	'options_callback' => CreateOptionsEventCallbackFactory::createCallback('tl_foo.some_select.create-options'),
);
```

Now you can fill the options with an event listener, listening on the event named `tl_foo.some_select.create-options`.

```php
$GLOBALS['TL_EVENTS']['tl_foo.some_select.create-options'][] = function($event) {
	$options = $event->getOptions();

	$options['value1'] = 'label 1';
	$options['value2'] = 'label 2';
	$options['value3'] = 'label 3';
};
```

Manipulate the options with a second event listener is pretty easy.

```php
$GLOBALS['TL_EVENTS']['tl_foo.some_select.create-options'][] = array(
	function($event) {
		$options = $event->getOptions();

		// remove a default value
		unset($options['value2']);

		// add a new value
		$options['value4'] = 'label 4';
	},
	-10 // we need a lower priority here, to make sure this listener is triggered after the default listener
);
```

See the [event dispatcher documentation](https://github.com/contao-community-alliance/event-dispatcher#listen-on-events)
for more examples how to listen on an event.

## Custom event

By default, an event of type `ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent` is used.
If you want your own event type, you can pass the class or a factory method as second parameter to `CreateOptionsEventCallbackFactory::createCallback()`.

First you need to write your own create-options event class.

```php
class MyCreateOptionsEvent extends \ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent
{
	protected $additionalData;

	function __construct($additionalData, \DataContainer $dataContainer, \ArrayObject $options = null)
	{
		parent::__construct($dataContainer, $options);
		$this->additionalData = $additionalData;
	}

	public function getAdditionalData()
	{
		return $this->additionalData;
	}
}
```

Then you need to add your factory to `CreateOptionsEventCallbackFactory::createCallback()`.

```php
use ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory;

$GLOBALS['TL_DCA']['tl_foo']['fields']['some_select'] = array(
	'inputType' => 'select',
	...
	'options_callback' => CreateOptionsEventCallbackFactory::createCallback(
		'tl_foo.some_select.create-options',
		function($dataContainer) {
			return new \MyCreateOptionsEvent(array('some' => 'value'), $dc);
		}
	),
);
```