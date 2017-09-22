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


namespace Eccube\Form\Type\Admin;

use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Form\Type\PriceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class PaymentRegisterType extends AbstractType
{
    /**
     * @var \Eccube\Application $app
     * @Inject(Application::class)
     */
    protected $app;

    public function setApp(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('method', TextType::class, array(
                'label' => '支払方法',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('rule_min', PriceType::class, array(
                'label' => false,
            ))
            ->add('rule_max', PriceType::class, array(
                'label' => false,
                'required' => false,
            ))
            ->add('payment_image_file', FileType::class, array(
                'label' => 'ロゴ画像',
                'mapped' => false,
                'required' => false,
            ))
            ->add('payment_image', HiddenType::class, array(
                'required' => false,
            ))
            ->add('charge_flg', HiddenType::class)
            ->add('fix_flg', HiddenType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, function($event) {
                $form = $event->getForm();
                $ruleMax = $form['rule_max']->getData();
                $ruleMin = $form['rule_min']->getData();
                if (!empty($ruleMin) && !empty($ruleMax) && $ruleMax < $ruleMin) {
                    $form['rule_min']->addError(new FormError('利用条件(下限)は'.$ruleMax.'円以下にしてください。'));
                }
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($app) {
                $form = $event->getForm();
                /** @var \Eccube\Entity\Payment $Payment */
                $Payment = $event->getData();
                if (is_null($Payment) || $Payment->getChargeFlg() == 1) {
                    $form->add('charge', PriceType::class, array(
                        'label' => '手数料',
                    ));
                } else {
                    $form->add('charge', HiddenType::class);
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Payment',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'payment_register';
    }
}
