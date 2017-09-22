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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Form\Validator\TwigLint;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class BlockType extends AbstractType
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
            ->add('name', TextType::class, array(
                'label' => 'ブロック名',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $this->appConfig['stext_len'],
                    ))
                )
            ))
            ->add('file_name', TextType::class, array(
                'label' => 'ファイル名',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $this->appConfig['stext_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9a-zA-Z\/_]+$/',
                    )),
                )
            ))
            ->add('block_html', HiddenType::class, array(
                'label' => 'ブロックデータ',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new TwigLint(),
                ]
            ))
            ->add('DeviceType', EntityType::class, array(
                'class' => 'Eccube\Entity\Master\DeviceType',
                'choice_label' => 'id',
            ))
            ->add('id', HiddenType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, function($event) use ($app) {
                $form = $event->getForm();
                $file_name = $form['file_name']->getData();
                $DeviceType = $form['DeviceType']->getData();
                $block_id = $form['id']->getData();

                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('b')
                    ->from('Eccube\\Entity\\Block', 'b')
                    ->where('b.file_name = :file_name')
                    ->setParameter('file_name', $file_name)
                    ->andWhere('b.DeviceType = :DeviceType')
                    ->setParameter('DeviceType', $DeviceType);
                if (isset($block_id)) {
                    $qb
                        ->andWhere('b.id <> :block_id')
                        ->setParameter('block_id', $block_id);
                }

                $Block = $qb
                    ->getQuery()
                    ->getResult();
                if (count($Block) > 0) {
                    $form['file_name']->addError(new FormError('※ 同じファイル名のデータが存在しています。別のファイル名を入力してください。'));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Block',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'block';
    }
}
