<?php
/**
 * Created by PhpStorm.
 * User: hideki_okajima
 * Date: 2018/06/21
 * Time: 13:52
 */

namespace Plugin\LinkPayment\Service;


use Eccube\Service\Payment\PaymentMethod;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PaymentService
{

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function dispatch(PaymentMethod $PaymentMethod)
    {
        return new RedirectResponse($this->router->generate('payment_company'));
    }
}