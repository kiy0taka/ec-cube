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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class MasterdataDataType extends AbstractType
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    public function setAppConfig(array $appConfig)
    {
        $this->appConfig = $appConfig;
    }

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
            ->add('id', TextType::class, array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->appConfig['int_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^\d+$/u',
                        'message' => $app->trans('form.type.numeric.invalid'),
                    )),
                ),
            ))
            ->add('name', TextType::class, array(
                'required' => false,
            ))
        ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($app) {
            $form = $event->getForm();
            $data = $form->getData();
            if (strlen($data['id']) && strlen($data['name']) == 0) {
                $form['name']->addError(new FormError('This value should not be blank.'));
            }

            if (strlen($data['name']) && strlen($data['id']) == 0) {
                $form['id']->addError(new FormError('This value should not be blank.'));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_system_masterdata_data';
    }
}
