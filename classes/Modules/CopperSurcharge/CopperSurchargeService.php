<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge;

use Xentral\Modules\CopperSurcharge\Data\CopperSurchargeData;
use Xentral\Modules\CopperSurcharge\Wrapper\CompanyDataWrapper;
use Xentral\Modules\SystemConfig\Exception\ConfigurationKeyNotFoundException;
use Xentral\Modules\SystemConfig\Exception\InvalidArgumentException;
use Xentral\Modules\SystemConfig\Exception\ValueTooLargeException;
use Xentral\Modules\SystemConfig\SystemConfigModule;

final class CopperSurchargeService
{

    /** @var string */
    private const NAMESPACE = 'coppersurcharge';

    /** @var SystemConfigModule $systemConfig */
    private $systemConfig;

    /** @var CompanyDataWrapper $companyDataWrapper */
    private $companyDataWrapper;

    /**
     * @param SystemConfigModule $systemConfig
     * @param CompanyDataWrapper $companyDataWrapper
     */
    public function __construct(SystemConfigModule $systemConfig, CompanyDataWrapper $companyDataWrapper)
    {
        $this->systemConfig = $systemConfig;
        $this->companyDataWrapper = $companyDataWrapper;
    }

    /**
     * @return CopperSurchargeData|null
     */
    public function findConfigurationData(): ?CopperSurchargeData
    {
        try {
            $articleId = (int)$this->systemConfig->getValue(
                self::NAMESPACE,
                'articleid'
            );
            $surchargePositionType = (int)$this->systemConfig->getValue(
                self::NAMESPACE,
                'surchargepositiontype'
            );
            $surchargeDocumentConversion = (int)$this->systemConfig->getValue(
                self::NAMESPACE,
                'surchargedocumentconversion'
            );
            $surchargeInvoice = (int)$this->systemConfig->getValue(
                self::NAMESPACE,
                'surchargeinvoice'
            );
            $surchargeDeliveryCosts = (float)$this->systemConfig->getValue(
                self::NAMESPACE,
                'surchargedeliverycosts'
            );
            $surchargeCopperBase = (string)$this->systemConfig->getValue(
                self::NAMESPACE,
                'surchargecopperbase'
            );
            $surchargeCopperBaseStandard = (float)$this->systemConfig->getValue(
                self::NAMESPACE,
                'surchargecopperbasestandard'
            );
            $copperNumberOption = (string)$this->systemConfig->getValue(
                self::NAMESPACE,
                'coppernumberoption'
            );
            $surchargeMaintenanceType = (int)$this->systemConfig->getValue(
                self::NAMESPACE,
                'surchargemaintenancetype'
            );
        } catch (ConfigurationKeyNotFoundException $e) {
            return null;
        }

        return new CopperSurchargeData(
            $articleId,
            $surchargePositionType,
            $surchargeDocumentConversion,
            $surchargeInvoice,
            $surchargeDeliveryCosts,
            $surchargeCopperBase,
            $surchargeCopperBaseStandard,
            $surchargeMaintenanceType,
            $copperNumberOption
        );
    }

    /**
     * @param CopperSurchargeData $copperSurchargeData
     *
     * @throws InvalidArgumentException
     * @throws ValueTooLargeException
     */
    public function setConfigurationData(CopperSurchargeData $copperSurchargeData)
    {
        $this->systemConfig->setValue(
            self::NAMESPACE,
            'articleid',
            (string)$copperSurchargeData->getCopperSurchargeArticleId()
        );
        $this->systemConfig->setValue(
            self::NAMESPACE,
            'surchargepositiontype',
            (string)$copperSurchargeData->getSurchargePositionType()
        );
        $this->systemConfig->setValue(
            self::NAMESPACE,
            'surchargedocumentconversion',
            (string)$copperSurchargeData->getSurchargeDocumentConversion()
        );
        $this->systemConfig->setValue(
            self::NAMESPACE,
            'surchargeinvoice',
            (string)$copperSurchargeData->getSurchargeInvoice()
        );
        $this->systemConfig->setValue(
            self::NAMESPACE,
            'surchargedeliverycosts',
            (string)$copperSurchargeData->getSurchargeDeliveryCosts()
        );
        $this->systemConfig->setValue(
            self::NAMESPACE,
            'surchargecopperbase',
            (string)$copperSurchargeData->getSurchargeCopperBase()
        );
        $this->systemConfig->setValue(
            self::NAMESPACE,
            'surchargecopperbasestandard',
            (string)$copperSurchargeData->getSurchargeCopperBaseStandard()
        );
        $this->systemConfig->setValue(
            self::NAMESPACE,
            'coppernumberoption',
            $copperSurchargeData->getCopperNumberOption()
        );
        $this->systemConfig->setValue(
            self::NAMESPACE,
            'surchargemaintenancetype',
            (string)$copperSurchargeData->getSurchargeMaintenanceType()
        );
    }

    /**
     * @param string $field
     *
     * @return string
     */
    public function findCompanyData(string $field): string
    {
        return $this->companyDataWrapper->getCompanyData($field);
    }
}
