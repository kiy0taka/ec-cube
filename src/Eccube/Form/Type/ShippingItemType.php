<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Form\Type;

use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Service\ShoppingService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class ShippingItemType extends AbstractType
{
    /**
     * @Inject(ShoppingService::class)
     * @var ShoppingService
     */
    protected $shoppingService;

    public function setShoppingService(ShoppingService $shoppingService)
    {
        $this->shoppingService = $shoppingService;
    }

    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function ($event) use ($app) {
                /** @var \Eccube\Entity\Shipping $data */
                $data = $event->getData();
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();

                // お届け日を取得
                $deliveryDates = $this->shoppingService->getFormDeliveryDates($data->getOrder());

                // 配送業者
                // 商品種別に紐づく配送業者を取得
                $delives = $this->shoppingService->getDeliveriesOrder($data->getOrder());

                $deliveries = array();
                foreach ($delives as $Delivery) {
                    foreach ($data->getShipmentItems() as $item) {
                        $productType = $item->getProductClass()->getProductType();
                        if ($Delivery->getProductType()->getId() == $productType->getId()) {
                            $deliveries[] = $Delivery;
                        }
                    }
                }

                $deliveryTimes = null;
                $delivery = $data->getDelivery();
                if ($delivery) {
                    $deliveryTimes = $delivery->getDeliveryTimes();
                }

                $form
                    ->add('delivery', EntityType::class, array(
                        'class' => 'Eccube\Entity\Delivery',
                        'choice_label' => 'name',
                        'choices' => $deliveries,
                        'data' => $delivery,
                        'constraints' => array(
                            new Assert\NotBlank(),
                        ),
                    ))
                    ->add('shippingDeliveryDate', ChoiceType::class, array(
                        'choices' => array_flip($deliveryDates),
                        'required' => false,
                        'placeholder' => '指定なし',
                        'mapped' => false,
                    ))
                    ->add('deliveryTime', EntityType::class, array(
                        'class' => 'Eccube\Entity\DeliveryTime',
                        'choice_label' => 'deliveryTime',
                        'choices' => $deliveryTimes,
                        'required' => false,
                        'placeholder' => '指定なし',
                    ));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                /** @var \Eccube\Entity\Shipping $data */
                $data = $event->getData();
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();

                if (is_null($data)) {
                    return;
                }

                $shippingDeliveryDate = $data->getShippingDeliveryDate();
                if (!empty($shippingDeliveryDate)) {
                    $form['shippingDeliveryDate']->setData($shippingDeliveryDate->format('Y/m/d'));
                }

            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                /** @var \Eccube\Entity\Shipping $data */
                $data = $event->getData();
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();
                $shippingDeliveryDate = $form['shippingDeliveryDate']->getData();
                if (!empty($shippingDeliveryDate)) {
                    $data->setShippingDeliveryDate(new \DateTime($form['shippingDeliveryDate']->getData()));
                } else {
                    $data->setShippingDeliveryDate(null);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Shipping',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shipping_item';
    }
}
