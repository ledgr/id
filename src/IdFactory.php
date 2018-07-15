<?php

declare(strict_types = 1);

namespace byrokrat\id;

/**
 * Create ID objects from raw id string
 */
class IdFactory implements IdFactoryInterface
{
    /**
     * @deprecated Will be removed in version 2. Use createId() instead.
     */
    public function create($raw)
    {
        trigger_error(
            'create() is deprecated and will be removed in version 2. Use createId() instead.',
            E_USER_DEPRECATED
        );

        return $this->createId($raw);
    }

    public function createId(string $raw): IdInterface
    {
        throw new Exception\UnableToCreateIdException("Unable to create ID for number '{$raw}'");
    }
}
