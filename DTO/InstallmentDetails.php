<?php


namespace RpayRatePay\DTO;


class InstallmentDetails
{

    protected $totalAmount;
    protected $amount;
    protected $interestRate;
    protected $interestAmount;
    protected $serviceCharge;
    protected $annualPercentageRate;
    protected $monthlyDebitInterest;
    protected $numberOfRatesFull;
    protected $rate;
    protected $lastRate;
    protected $paymentSubtype;
    protected $paymentFirstday;
    protected $dueDate;

    /**
     * @return mixed
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param mixed $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getInterestRate()
    {
        return $this->interestRate;
    }

    /**
     * @param mixed $interestRate
     */
    public function setInterestRate($interestRate)
    {
        $this->interestRate = $interestRate;
    }

    /**
     * @return mixed
     */
    public function getInterestAmount()
    {
        return $this->interestAmount;
    }

    /**
     * @param mixed $interestAmount
     */
    public function setInterestAmount($interestAmount)
    {
        $this->interestAmount = $interestAmount;
    }

    /**
     * @return mixed
     */
    public function getServiceCharge()
    {
        return $this->serviceCharge;
    }

    /**
     * @param mixed $serviceCharge
     */
    public function setServiceCharge($serviceCharge)
    {
        $this->serviceCharge = $serviceCharge;
    }

    /**
     * @return mixed
     */
    public function getAnnualPercentageRate()
    {
        return $this->annualPercentageRate;
    }

    /**
     * @param mixed $annualPercentageRate
     */
    public function setAnnualPercentageRate($annualPercentageRate)
    {
        $this->annualPercentageRate = $annualPercentageRate;
    }

    /**
     * @return mixed
     */
    public function getMonthlyDebitInterest()
    {
        return $this->monthlyDebitInterest;
    }

    /**
     * @param mixed $monthlyDebitInterest
     */
    public function setMonthlyDebitInterest($monthlyDebitInterest)
    {
        $this->monthlyDebitInterest = $monthlyDebitInterest;
    }

    /**
     * @return mixed
     */
    public function getNumberOfRatesFull()
    {
        return $this->numberOfRatesFull;
    }

    /**
     * @param mixed $numberOfRatesFull
     */
    public function setNumberOfRatesFull($numberOfRatesFull)
    {
        $this->numberOfRatesFull = $numberOfRatesFull;
    }

    /**
     * @return mixed
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param mixed $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return mixed
     */
    public function getLastRate()
    {
        return $this->lastRate;
    }

    /**
     * @param mixed $lastRate
     */
    public function setLastRate($lastRate)
    {
        $this->lastRate = $lastRate;
    }

    /**
     * @return mixed
     */
    public function getPaymentSubtype()
    {
        return $this->paymentSubtype;
    }

    /**
     * @param mixed $paymentSubtype
     */
    public function setPaymentSubtype($paymentSubtype)
    {
        $this->paymentSubtype = $paymentSubtype;
    }

    /**
     * @return mixed
     */
    public function getPaymentFirstday()
    {
        return $this->paymentFirstday;
    }

    /**
     * @param mixed $paymentFirstday
     */
    public function setPaymentFirstday($paymentFirstday)
    {
        $this->paymentFirstday = $paymentFirstday;
    }

    /**
     * @return mixed
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @param mixed $dueDate
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
    }

}
