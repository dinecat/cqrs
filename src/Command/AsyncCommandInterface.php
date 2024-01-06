<?php

declare(strict_types=1);

namespace Dinecat\CQRS\Command;

/**
 * Base interface for asynchronous commands.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
interface AsyncCommandInterface extends CommandInterface {}
