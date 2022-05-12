<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Data;

use Xentral\Modules\SuperSearch\Exception\InvalidArgumentException;

final class IndexIdentifier
{
    /** @var string $name */
    private $name;

    /** @var int $id */
    private $id;

    /**
     * @param string     $name
     * @param int|string $id
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name, $id)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Invalid argument value. $name parameter can not be empty.');
        }
        if (empty($id)) {
            throw new InvalidArgumentException('Invalid argument value. $id parameter can not be empty.');
        }
        if (strlen($name) > 16) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument value "%s". Max length for $id is 16 characters.', $name
            ));
        }
        if (strlen($id) > 38) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument value "%s". Max length for $id is 38 characters.', $id
            ));
        }
        if (preg_match('/[^a-z]+/', $name) === 1) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument value "%s". Valid characters for $name parameter: a-z', $name
            ));
        }

        $this->name = (string)$name;
        $this->id = is_numeric($id) ? (int)$id : (string)$id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }
}
