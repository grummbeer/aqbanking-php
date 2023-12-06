<?php

namespace AqBanking;

use Money\Money;

class Transaction implements Arrayable
{
    /**
     * @var Account
     */
    private $localAccount;

    /**
     * @var Account
     */
    private $remoteAccount;

    /**
     * @var string
     */
    private $purpose;

    /**
     * @var \DateTime
     */
    private $valutaDate;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var Money
     */
    private $value;

    /**
     * @var string
     */
    private $primaNota;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $customerReference;

    public function __construct(
        Account $localAccount,
        Account $remoteAccount,
        $type,
        $purpose,
        \DateTime $valutaDate = null,
        \DateTime $date,
        Money $value,
        $primaNota,
        $customerReference
    ) {
        $this->date = $date;
        $this->localAccount = $localAccount;
        $this->purpose = $purpose;
        $this->remoteAccount = $remoteAccount;
        $this->type = $type;
        $this->value = $value;
        $this->valutaDate = $valutaDate;
        $this->primaNota = $primaNota;
        $this->customerReference = $customerReference;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return \AqBanking\Account
     */
    public function getLocalAccount()
    {
        return $this->localAccount;
    }

    /**
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @return \AqBanking\Account
     */
    public function getRemoteAccount()
    {
        return $this->remoteAccount;
    }

    /**
     * @return \Money\Money
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \DateTime
     */
    public function getValutaDate()
    {
        return $this->valutaDate;
    }

    /**
     * @return string
     */
    public function getPrimaNota()
    {
        return $this->primaNota;
    }

    /**
     * @return string
     */
    public function getCustomerReference()
    {
        return $this->customerReference;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function toArray()
    {
        return [
            'date' => $this->getDate()->format('Y-m-d'),
            'localAccount' => $this->getLocalAccount()->toArray(),
            'purpose' => $this->getPurpose(),
            'remoteAccount' => $this->getRemoteAccount()->toArray(),
            'type' => $this->getType(),
            'value' => [
                'amount' => $this->getValue()->getAmount(),
                'currency' => $this->getValue()->getCurrency()->getName(),
                'priceUnit' => 100,
            ],
            'valutaDate' => $this->getValutaDate() ? $this->getValutaDate()->format('Y-m-d') : null,
            'primaNota' => $this->getPrimaNota(),
            'customerReference' => $this->getCustomerReference(),
        ];
    }
}
