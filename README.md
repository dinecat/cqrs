# Messenger Component

Just extension over Symfony Messenger for provide Command, Query and Event message buses.

##Using in Symfony project

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
                        - 'Dinecat\Messenger\Middleware\SecondLevelValidationMiddleware' # If you need second level.
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
            Dinecat\Messenger\CommandBus\CommandMessageHandlerInterface:
                tags:
                    - { name: 'messenger.message_handler', bus: 'messenger.bus.commands' }
    
            Dinecat\Messenger\QueryBus\QueryMessageHandlerInterface:
                tags:
                    - { name: 'messenger.message_handler', bus: 'messenger.bus.queries' }
    
            Dinecat\Messenger\EventBus\EventMessageHandlerInterface:
                tags:
                    - { name: 'messenger.message_handler', bus: 'messenger.bus.events' }
    ```
