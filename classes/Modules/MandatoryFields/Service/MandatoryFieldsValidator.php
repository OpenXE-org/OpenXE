<?php

declare(strict_types=1);

namespace Xentral\Modules\MandatoryFields\Service;

use Xentral\Modules\MandatoryFields\Data\MandatoryFieldData;
use Xentral\Modules\MandatoryFields\Data\ValidatorResultData;
use Xentral\Modules\MandatoryFields\Exception\MandatoryFieldNotFoundException;
use Xentral\Modules\MandatoryFields\Exception\UnknownTypeException;

final class MandatoryFieldsValidator
{
    /** @var MandatoryFieldsGateway $gateway */
    private $gateway;

    /**
     * @param MandatoryFieldsGateway $gateway
     */
    public function __construct(
        MandatoryFieldsGateway $gateway
    ) {
        $this->gateway = $gateway;
    }

    /**
     * @param string $type
     * @param string $value
     * @param int    $mandatoryFieldId
     *
     * @throws UnknownTypeException
     * @throws MandatoryFieldNotFoundException
     *
     * @return ValidatorResultData
     */
    public function validate(string $type, string $value, int $mandatoryFieldId): ValidatorResultData
    {
        $mandatoryField = $this->gateway->getById($mandatoryFieldId);

        if ($mandatoryField->isMandatory() || (!$mandatoryField->isMandatory() && $value != '')) {
            switch ($type) {
                case 'kunde':
                    $result = $this->checkCustomer($value);
                    break;
                case 'mitarbeiter':
                    $result = $this->checkEmployee($value);
                    break;
                case 'artikel':
                    $result = $this->checkArticle($value);
                    break;
                case 'rechnung':
                    $result = $this->checkInvoice($value);
                    break;
                case 'auftrag':
                    $result = $this->checkOrder($value);
                    break;
                case 'text':
                    $result = $this->checkText($value, $mandatoryField);
                    break;
                case 'ganzzahl':
                    $result = $this->checkInteger($value, $mandatoryField);
                    break;
                case 'dezimalzahl':
                    $result = $this->checkDecimal($value, $mandatoryField);
                    break;
                case 'datum':
                    $result = $this->checkDate($value);
                    break;
                case 'e-mail':
                    $result = $this->checkEmail($value);
                    break;
                default:
                    throw new UnknownTypeException('Type not known: ' . $type);
            }

            $error = $result['error'];
            $message = $result['default'];

            if (!empty($mandatoryField->getErrorMessage())) {
                $message = $mandatoryField->getErrorMessage();
            }

            return new ValidatorResultData($error, $message);
        }

        return new ValidatorResultData(false, '');
    }

    /**
     * @param string $value
     *
     * @return array
     */
    private function checkCustomer(string $value): array
    {
        $data['default'] = '';
        $data['error'] = false;

        if (!$this->gateway->isAddressWithCustomerNumberActive($value)) {
            $data['default'] = 'Kunde nicht gültig.';
            $data['error'] = true;
        }

        return $data;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    private function checkEmployee(string $value): array
    {
        $data['default'] = '';
        $data['error'] = false;

        if (!$this->gateway->isAddressWithEmployeeNumberActive($value)) {
            $data['default'] = 'Mitarbeiter nicht gültig.';
            $data['error'] = true;
        }

        return $data;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    private function checkArticle(string $value): array
    {
        $data['default'] = '';
        $data['error'] = false;

        if (!$this->gateway->isArticleWithNumberActive($value)) {
            $data['default'] = 'Artikel-Nr. nicht gültig.';
            $data['error'] = true;
        }

        return $data;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    private function checkInvoice(string $value): array
    {
        $data['default'] = '';
        $data['error'] = false;

        if (!$this->gateway->isInvoiceWithDocumentNumberActive($value)) {
            $data['default'] = 'Rechnungs-Nr. nicht gültig.';
            $data['error'] = true;
        }

        return $data;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    private function checkOrder(string $value): array
    {
        $data['default'] = '';
        $data['error'] = false;

        if (!$this->gateway->isOrderWithDocumentNumberActive($value)) {
            $data['default'] = 'Auftrags-Nr. nicht gültig.';
            $data['error'] = true;
        }

        return $data;
    }

    /**
     * @param string             $value
     * @param MandatoryFieldData $mandatoryField
     *
     * @return array
     */
    private function checkText(string $value, MandatoryFieldData $mandatoryField): array
    {
        $data['default'] = '';
        $data['error'] = false;

        $minLength = $mandatoryField->getMinLength();
        $maxLength = $mandatoryField->getMaxLength();


        if ($minLength > 0) {
            if (strlen($value) < $minLength) {
                $data['default'] = 'Eingabe zu kurz';
                $data['error'] = true;
            }
        }

        if ($maxLength > 0) {
            if (strlen($value) > $maxLength) {
                $data['default'] = 'Eingabe zu lang';
                $data['error'] = true;
            }
        }

        return $data;
    }

    /**
     * @param string             $value
     * @param MandatoryFieldData $mandatoryField
     *
     * @return array
     */
    private function checkInteger(string $value, MandatoryFieldData $mandatoryField): array
    {
        $data['default'] = '';
        $data['error'] = false;

        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $data['default'] = 'Keine Ganzzahl';
            $data['error'] = true;
        } else {
            $data = $this->validateNumbers($mandatoryField, $data, $value);
        }

        return $data;
    }

    /**
     * @param string             $value
     * @param MandatoryFieldData $mandatoryField
     *
     * @return array
     */
    private function checkDecimal(string $value, MandatoryFieldData $mandatoryField): array
    {
        $data['default'] = '';
        $data['error'] = false;

        if (strpos($value, '.') === false && strpos($value, ',') === false) {
            $data['default'] = 'Keine Dezimalzahl';
            $data['error'] = true;
        } else {
            $data = $this->validateNumbers($mandatoryField, $data, $value);
        }

        return $data;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    private function checkDate(string $value): array
    {
        $data['default'] = '';
        $data['error'] = false;

        $datum = str_replace('.', '-', $value);
        $datum = str_replace('/', '-', $datum);

        if (!(bool)strtotime($datum)) {
            $data['default'] = 'Datumsformat nicht gültig.';
            $data['error'] = true;
        }

        return $data;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    private function checkEmail(string $value): array
    {
        $data['default'] = '';
        $data['error'] = false;

        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $data['default'] = 'E-Mail Adressformat nicht gültig.';
            $data['error'] = true;
        }

        return $data;
    }


    /**
     * @param MandatoryFieldData $mandatoryField
     * @param array              $data
     * @param string             $value
     *
     * @return array
     */
    private function validateNumbers(MandatoryFieldData $mandatoryField, array $data, string $value): array
    {
        $comparator = $mandatoryField->getComparator();
        $compareTo = (float)$mandatoryField->getCompareto();
        $isValid = true;

        if ($comparator != '') {
            if ($comparator === 'equals') {
                $isValid = (float)$value == $compareTo;
            } elseif ($comparator === 'lowerthan') {
                $isValid = (float)$value < $compareTo;
            } elseif ($comparator === 'greaterthan') {
                $isValid = (float)$value > $compareTo;
            }

            if (!$isValid) {
                $data['error'] = true;
                $data['default'] = 'Die Zahl entspricht nicht den Vorgaben.';
            }
        }

        return $data;
    }
}
