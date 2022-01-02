# CQRS Component

Extension over Symfony Messenger component for provide Command, Query and Event message buses and related functionality.

## Using in Symfony project

See: https://symfony.com/doc/current/messenger/multiple_buses.html

### Setup buses in config/packages/messenger.php (or yaml)

```php
$messenger = $framework->messenger();

$messenger->defaultBus('bus.command');
$messenger->bus('bus.command')->middleware()->id('validation');
$messenger->bus('bus.query')->middleware()->id('validation');
$messenger->bus('bus.event')->defaultMiddleware('allow_no_handlers');
```

```yaml
framework:
    messenger:
        default_bus: 'bus.command'
        buses:
            bus.command:
                middleware:
                    - 'validation'
                    - 'Dinecat\Cqrs\Middleware\SecondLayerValidationMiddleware' # If you need second level.
            bus.query:
                middleware:
                    - 'validation'
            bus.event:
                default_middleware: 'allow_no_handlers'
                middleware: []
```

### Restrict handlers per bus in config/services.php (or yaml)

```php
$services
    ->instanceof(CommandHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus' => 'bus.command']);
$services
    ->instanceof(QueryHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus' => 'bus.query']);
$services
    ->instanceof(EventHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus' => 'bus.event']);
```

```yaml
services:
    _instanceof:
        Dinecat\Cqrs\CommandBus\CommandHandlerInterface:
            tags:
                - { name: 'messenger.message_handler', bus: 'bus.command' }

        Dinecat\Cqrs\QueryBus\QueryHandlerInterface:
            tags:
                - { name: 'messenger.message_handler', bus: 'bus.query' }

        Dinecat\Cqrs\EventBus\EventHandlerInterface:
            tags:
                - { name: 'messenger.message_handler', bus: 'bus.event' }
```
