# CQRS Component

Extension over Symfony Messenger component for provide Command, Query and Event message buses and related functionality.

## Using in Symfony project

See: https://symfony.com/doc/current/messenger.html#multiple-buses-command-event-buses

### Setup buses in config/packages/messenger.php (or yaml)

```php
$messenger = $framework->messenger();

$commandBus = $messenger->bus('command.bus');
$commandBus->middleware()->id('validation');
$commandBus->middleware()->id('doctrine_transaction');

$queryBus = $messenger->bus('query.bus');
$queryBus->middleware()->id('validation');

$eventBus = $messenger->bus('event.bus');
$eventBus->defaultMiddleware()->enabled(true)->allowNoHandlers(true)->allowNoSenders(true);
```

```yaml
framework:
    messenger:
        default_bus: 'command.bus'
        buses:
            command.bus:
                middleware:
                    - 'validation'
            query.bus:
                middleware:
                    - 'validation'
            event.bus:
                default_middleware:
                    enabled: true
                    allow_no_handlers: true
                    allow_no_senders: true
                middleware: []
```

### Restrict handlers per bus in config/services.php (or yaml)

```php
$services
    ->instanceof(CommandHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus' => 'command.bus']);
$services
    ->instanceof(QueryHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus' => 'query.bus']);
$services
    ->instanceof(EventHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus' => 'event.bus']);
```

```yaml
services:
    _instanceof:
        Dinecat\CQRS\CommandBus\CommandHandlerInterface:
            tags:
                - { name: 'messenger.message_handler', bus: 'command.bus' }

        Dinecat\CQRS\QueryBus\QueryHandlerInterface:
            tags:
                - { name: 'messenger.message_handler', bus: 'query.bus' }

        Dinecat\CQRS\EventBus\EventHandlerInterface:
            tags:
                - { name: 'messenger.message_handler', bus: 'event.bus' }
```
