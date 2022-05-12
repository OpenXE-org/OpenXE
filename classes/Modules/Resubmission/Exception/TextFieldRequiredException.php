<?php

namespace Xentral\Modules\Resubmission\Exception;

use DomainException;

class TextFieldRequiredException extends DomainException implements ResubmissionExceptionInterface
{
    /** @var string $fieldLabel */
    private $fieldLabel;

    /** @var string $requiredStageName */
    private $requiredStageName;

    /**
     * Exception wird geworfen wenn ein benötigtes Freitextfeld beim Speichern leer ist.
     *
     * @param string $fieldLabel
     * @param string $requiredStageName
     *
     * @return TextFieldRequiredException
     */
    public static function onEmpty($fieldLabel, $requiredStageName)
    {
        $instance = new self(sprintf(
            'The text field "%s" can not be saved. The text field is required from stage "%s" on.',
            $fieldLabel,
            $requiredStageName
        ));

        $instance->fieldLabel = $fieldLabel;
        $instance->requiredStageName = $requiredStageName;

        return $instance;
    }

    /**
     * @return string
     */
    public function getFieldLabel()
    {
        return $this->fieldLabel;
    }

    /**
     * @return string
     */
    public function getRequiredStageName()
    {
        return $this->requiredStageName;
    }
}
