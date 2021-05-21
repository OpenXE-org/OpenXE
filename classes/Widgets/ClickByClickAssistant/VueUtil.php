<?php

declare(strict_types=1);

namespace Xentral\Widgets\ClickByClickAssistant;

use Xentral\Widgets\ClickByClickAssistant\Exception\InvalidArgumentException;

final class VueUtil
{
    /**
     * @param array $array
     *
     * @return array
     */
    public static function keyValueArrayToVueOptions($array): array
    {
        self::ensureScalarKeyValueArray($array);

        $ret = [];
        foreach ($array as $value => $text) {
            $ret[] = ['value' => (string)$value, 'text' => (string)$text,];
        }

        return $ret;
    }

    /**
     * @param array[] $pageArray
     *
     * @return array
     */
    public static function getInputNamesFromVuePages($pageArray): array
    {
        self::ensureArray($pageArray);

        $ret = [];

        foreach ($pageArray as $page) {
            if (empty($page['inputs'])) {
                continue;
            }

            self::ensureArray($page['inputs']);
            foreach ($page['inputs'] as $input) {
                if (isset($input['name'])) {
                    $ret[] = $input['name'];
                }
            }
        }

        return $ret;
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private static function ensureArray($value): void
    {
        $type = gettype($value);
        if ($type !== 'array') {
            throw new InvalidArgumentException(sprintf('Wrong type "%s". Only "array" is allowed.', $type));
        }
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private static function ensureScalarKeyValueArray($value): void
    {
        self::ensureArray($value);

        foreach ($value as $key => $val) {
            if ($val !== null && !is_scalar($val)) {
                $type = gettype($val);
                throw new InvalidArgumentException(sprintf('Wrong type "%s". Only scalar types ar allowed.', $type));
            }
        }
    }
}
