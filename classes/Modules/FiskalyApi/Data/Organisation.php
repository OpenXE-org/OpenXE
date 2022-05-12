<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data;

class Organisation
{
    /** @var string $uuid */
    private $uuid;

    /** @var string $type */
    private $type;

    /** @var array $envs */
    private $envs;

    /** @var string $name */
    private $name;

    /** @var string $addressLine1 */
    private $addressLine1;

    /** @var string|null $addressLine2 */
    private $addressLine2;

    /** @var string $zip */
    private $zip;

    /** @var string $town */
    private $town;

    /** @var string $state */
    private $state;

    /** @var string $countryCode */
    private $countryCode;

    /** @var string|null $displayName */
    private $displayName;

    /** @var string|null $vatId */
    private $vatId;

    /** @var string|null $taxNumber */
    private $taxNumber;

    /** @var string|null $economyId */
    private $economyId;

    /** @var string|null $billingAddressId */
    private $billingAddressId;

    /** @var string|null $managedByOrganizationId */
    private $managedByOrganizationId;

    /** @var string|null $createdByUser */
    private $createdByUser;

    /** @var string|null $gln */
    private $gln;

    /** @var string|null $withholdBilling */
    private $withholdBilling;

    /** @var string|null $billToOrganization */
    private $billToOrganization;

    /** @var string|null $contactPersonId */
    private $contactPersonId;

    /**
     * Organisation constructor.
     *
     * @param string      $uuid
     * @param string      $type
     * @param array       $envs
     * @param string      $name
     * @param string      $addressLine1
     * @param string      $zip
     * @param string      $town
     * @param string      $state
     * @param string      $countryCode
     * @param string|null $addressLine2
     * @param string|null $displayName
     * @param string|null $vatId
     * @param string|null $taxNumber
     * @param string|null $economyId
     * @param string|null $billingAddressId
     * @param string|null $managedByOrganizationId
     * @param string|null $createdByUser
     * @param string|null $gln
     * @param string|null $withholdBilling
     * @param string|null $billToOrganization
     * @param string|null $contactPersonId
     */
    public function __construct(
        string $uuid,
        string $type,
        array $envs,
        string $name,
        string $addressLine1,
        string $zip,
        string $town,
        string $state,
        string $countryCode,
        ?string $addressLine2 = null,
        ?string $displayName = null,
        ?string $vatId = null,
        ?string $taxNumber = null,
        ?string $economyId = null,
        ?string $billingAddressId = null,
        ?string $managedByOrganizationId = null,
        ?string $createdByUser = null,
        ?string $gln = null,
        ?string $withholdBilling = null,
        ?string $billToOrganization = null,
        ?string $contactPersonId = null
    ) {
        $this->uuid = $uuid;
        $this->type = $type;
        $this->envs = $envs;
        $this->name = $name;
        $this->addressLine1 = $addressLine1;
        $this->zip = $zip;
        $this->town = $town;
        $this->state = $state;
        $this->countryCode = $countryCode;
        $this->addressLine2 = $addressLine2;
        $this->displayName = $displayName;
        $this->vatId = $vatId;
        $this->taxNumber = $taxNumber;
        $this->economyId = $economyId;
        $this->billingAddressId = $billingAddressId;
        $this->managedByOrganizationId = $managedByOrganizationId;
        $this->createdByUser = $createdByUser;
        $this->gln = $gln;
        $this->withholdBilling = $withholdBilling;
        $this->billToOrganization = $billToOrganization;
        $this->contactPersonId = $contactPersonId;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->_id,
            $apiResult->_type,
            $apiResult->_envs,
            $apiResult->name,
            $apiResult->address_line1,
            $apiResult->zip,
            $apiResult->town,
            $apiResult->state,
            $apiResult->country_code,
            $apiResult->address_line2 ?? null,
            $apiResult->display_name ?? null,
            $apiResult->vat_id ?? null,
            $apiResult->tax_number ?? null,
            $apiResult->economy_id ?? null,
            $apiResult->billing_address_id ?? null,
            $apiResult->managed_by_organization_id ?? null,
            $apiResult->created_by_user ?? null,
            $apiResult->billing_options->gln ?? null,
            $apiResult->billing_options->withhold_billing ?? null,
            $apiResult->billing_options->bill_to_organization ?? null,
            $apiResult->contactPersonId ?? null
        );
    }

    /**
     * @param array $dbState
     *
     * @return $this
     */
    public static function fromDbState(array $dbState): self
    {
        return new self(
            $dbState['_id'],
            $dbState['_type'],
            $dbState['_envs'],
            $dbState['name'],
            $dbState['address_line1'],
            $dbState['zip'],
            $dbState['town'],
            $dbState['state'],
            $dbState['country_code'],
            $dbState['address_line2'] ?? null,
            $dbState['display_name'] ?? null,
            $dbState['vat_id'] ?? null,
            $dbState['tax_number'] ?? null,
            $dbState['economy_id'] ?? null,
            $dbState['billing_address_id'] ?? null,
            $dbState['managed_by_organization_id'] ?? null,
            $dbState['created_by_user'] ?? null,
            $dbState['billing_options']['gln'] ?? null,
            $dbState['billing_options']['withhold_billing'] ?? null,
            $dbState['billing_options']['bill_to_organization'] ?? null,
            $dbState['contactPersonId'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            '_id'           => $this->getUuid(),
            '_type'         => $this->getType(),
            '_envs'         => $this->getEnvs(),
            'name'          => $this->getName(),
            'address_line1' => $this->getAddressLine1(),
            'zip'           => $this->getZip(),
            'town'          => $this->getTown(),
            'country_code'  => $this->getCountryCode(),
        ];
        if ($this->displayName !== null) {
            $dbState['display_name'] = $this->getDisplayName();
        }
        if ($this->vatId !== null) {
            $dbState['vat_id'] = $this->getVatId();
        }
        if ($this->contactPersonId !== null) {
            $dbState['contact_person_id'] = $this->getContactPersonId();
        }
        if ($this->addressLine2 !== null) {
            $dbState['address_line2'] = $this->getAddressLine2();
        }
        if ($this->state !== null) {
            $dbState['state'] = $this->getState();
        }
        if ($this->taxNumber !== null) {
            $dbState['tax_number'] = $this->getTaxNumber();
        }
        if ($this->economyId !== null) {
            $dbState['economy_id'] = $this->getEconomyId();
        }
        if ($this->gln !== null) {
            $dbState['billing_options']['gln'] = $this->getGln();
        }
        if ($this->withholdBilling !== null) {
            $dbState['billing_options']['withhold_billing'] = $this->getWithholdBilling();
        }
        if ($this->billToOrganization !== null) {
            $dbState['billing_options']['bill_to_organization'] = $this->getBillToOrganization();
        }
        if ($this->billingAddressId !== null) {
            $dbState['billing_address_id'] = $this->getBillingAddressId();
        }
        if ($this->managedByOrganizationId !== null) {
            $dbState['managed_by_organization_id'] = $this->getManagedByOrganizationId();
        }
        if ($this->createdByUser !== null) {
            $dbState['created_by_user'] = $this->getCreatedByUser();
        }

        return $dbState;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getEnvs(): array
    {
        return $this->envs;
    }

    /**
     * @param array $envs
     */
    public function setEnvs(array $envs): void
    {
        $this->envs = $envs;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    /**
     * @param string $addressLine1
     */
    public function setAddressLine1(string $addressLine1): void
    {
        $this->addressLine1 = $addressLine1;
    }

    /**
     * @return string|null
     */
    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    /**
     * @param string|null $addressLine2
     */
    public function setAddressLine2(?string $addressLine2): void
    {
        $this->addressLine2 = $addressLine2;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getTown(): string
    {
        return $this->town;
    }

    /**
     * @param string $town
     */
    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     */
    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string|null
     */
    public function getVatId(): ?string
    {
        return $this->vatId;
    }

    /**
     * @param string|null $vatId
     */
    public function setVatId(?string $vatId): void
    {
        $this->vatId = $vatId;
    }

    /**
     * @return string|null
     */
    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    /**
     * @param string|null $taxNumber
     */
    public function setTaxNumber(?string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    /**
     * @return string|null
     */
    public function getEconomyId(): ?string
    {
        return $this->economyId;
    }

    /**
     * @param string|null $economyId
     */
    public function setEconomyId(?string $economyId): void
    {
        $this->economyId = $economyId;
    }

    /**
     * @return string|null
     */
    public function getBillingAddressId(): ?string
    {
        return $this->billingAddressId;
    }

    /**
     * @param string|null $billingAddressId
     */
    public function setBillingAddressId(?string $billingAddressId): void
    {
        $this->billingAddressId = $billingAddressId;
    }

    /**
     * @return string|null
     */
    public function getManagedByOrganizationId(): ?string
    {
        return $this->managedByOrganizationId;
    }

    /**
     * @param string|null $managedByOrganizationId
     */
    public function setManagedByOrganizationId(?string $managedByOrganizationId): void
    {
        $this->managedByOrganizationId = $managedByOrganizationId;
    }

    /**
     * @return string|null
     */
    public function getCreatedByUser(): ?string
    {
        return $this->createdByUser;
    }

    /**
     * @param string|null $createdByUser
     */
    public function setCreatedByUser(?string $createdByUser): void
    {
        $this->createdByUser = $createdByUser;
    }

    /**
     * @return string|null
     */
    public function getGln(): ?string
    {
        return $this->gln;
    }

    /**
     * @param string|null $gln
     */
    public function setGln(?string $gln): void
    {
        $this->gln = $gln;
    }

    /**
     * @return string|null
     */
    public function getWithholdBilling(): ?string
    {
        return $this->withholdBilling;
    }

    /**
     * @param string|null $withholdBilling
     */
    public function setWithholdBilling(?string $withholdBilling): void
    {
        $this->withholdBilling = $withholdBilling;
    }

    /**
     * @return string|null
     */
    public function getBillToOrganization(): ?string
    {
        return $this->billToOrganization;
    }

    /**
     * @param string|null $billToOrganization
     */
    public function setBillToOrganization(?string $billToOrganization): void
    {
        $this->billToOrganization = $billToOrganization;
    }

    /**
     * @return string|null
     */
    public function getContactPersonId(): ?string
    {
        return $this->contactPersonId;
    }

    /**
     * @param string|null $contactPersonId
     */
    public function setContactPersonId(?string $contactPersonId): void
    {
        $this->contactPersonId = $contactPersonId;
    }
}
