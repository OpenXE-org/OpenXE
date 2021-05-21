<?php

namespace Xentral\Widgets\SuperSearch\Query;

use Xentral\Widgets\SuperSearch\Exception\InvalidArgumentException;

final class DetailQuery
{
    /** @var string $groupKey */
    private $groupKey;

    /** @var int|string $itemIdentifier */
    private $itemIdentifier;

    /**
     * @param string     $groupKey
     * @param int|string $itemIdentifier
     *
     * @throws InvalidArgumentException
     */
    public function __construct($groupKey, $itemIdentifier)
    {
        $groupKeyCleaned = (string)preg_replace('#[^a-z_]#', '', $groupKey);
        if ($groupKey !== $groupKeyCleaned) {
            throw new InvalidArgumentException(
                'Invalid characters in $groupKey. Allowed characters: a-z and underscore.'
            );
        }
        if (!is_int($itemIdentifier) && !is_string($itemIdentifier)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument type for $itemIdentifier. Only integer or string is allowed. Type given: %s.',
                gettype($itemIdentifier)
            ));
        }

        $this->groupKey = (string)$groupKey;
        $this->itemIdentifier = $itemIdentifier;
    }

    /**
     * @return string
     */
    public function getGroupKey()
    {
        return $this->groupKey;
    }

    /**
     * @return int|string
     */
    public function getItemIdentifier()
    {
        return $this->itemIdentifier;
    }
}
