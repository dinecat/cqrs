# CQRS Component

Just extension over Symfony Messenger for provide Command, Query and Event message buses and related structures.

## Using in Symfony project

See: https://symfony.com/doc/current/messenger/multiple_buses.html

1. Setup buses in config/packages/messenger.yaml

    ```yaml
    framework:
        messenger:
            default_bus: 'messenger.bus.commands'
            buses:
                messenger.bus.commands:
                    middleware:
                        - 'validation'
                        - 'Dinecat\Cqrs\Middleware\SecondLevelValidationMiddleware' # If you need second level.
                messenger.bus.queries:
                    middleware:
                        - 'validation'
                messenger.bus.events:
                    default_middleware: 'allow_no_handlers'
                    middleware: []
    ```

2. Restrict handlers per bus in config/services.yaml

    ```yaml
    services:
        _instanceof:
            Dinecat\Cqrs\CommandBus\CommandHandlerInterface:
                tags:
                    - { name: 'messenger.message_handler', bus: 'messenger.bus.commands' }
    
            Dinecat\Cqrs\QueryBus\QueryHandlerInterface:
                tags:
                    - { name: 'messenger.message_handler', bus: 'messenger.bus.queries' }
    
            Dinecat\Cqrs\EventBus\EventHandlerInterface:
                tags:
                    - { name: 'messenger.message_handler', bus: 'messenger.bus.events' }
    ```
