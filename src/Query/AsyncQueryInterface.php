<?php

declare(strict_types=1);

namespace Dinecat\CQRS\Query;

/**
 * Base interface for asynchronous queries.
 *
 * Means that execution of query does not return any direct result.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
interface AsyncQueryInterface extends QueryInterface {}
