<?php
namespace Eccube\Service;

use Eccube\Annotation\Service;
use Eccube\Service\Calculator\CalculateContext;
use Eccube\Service\Calculator\Strategy\CalculateStrategyInterface;

/**
 * @Service
 */
class CalculateService
{
    protected $Customer;
    protected $Order;
    protected $ProductClasses = [];
    protected $Deliveries = [];
    protected $Payment;

    /** @var CalculateContext */
    protected $CalculateContext;

    public function setCalculateContext(CalculateContext $CalculateContext)
    {
        $this->CalculateContext = $CalculateContext;
    }

    public function __construct($Order, $Customer)
    {
        $this->Order = $Order;
        $this->Customer = $Customer;
    }
    public function addCalculator(CalculateStrategyInterface $strategy)
    {
        $Strategies = $this->CalculateContext->getCalculateStrategies();
        $Strategies->add($strategy);
        $this->CalculateContext->setCalculateStrategies($Strategies);
    }

    /**
     * 単価集計後の Order を返す.
     *
     * @return \Eccube\Entity\Order
     */
    public function calculate()
    {
        $Order = $this->CalculateContext->executeCalculator();
        return $Order;
    }

    public function setContext(CalculateContext $Context)
    {
        $this->CalculateContext = $Context;
    }
}
