<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Form\Type;


use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class ProductReviewType extends AbstractType
{
    /**
     * @var array
     * @Inject("config")
     */
    private $config;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('reviewer_name', TextType::class, array(
                'label' => '投稿者名',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $this->config['stext_len'])),
                ),
                'attr' => array(
                    'maxlength' => $this->config['stext_len'],
                ),
            ))
            ->add('recommend_level', ChoiceType::class, array(
                'label' => 'おすすめレベル',
                'choices' => array(
                    '★★★★★' => 5,
                    '★★★★' => 4,
                    '★★★' => 3,
                    '★★' => 2,
                    '★' => 1,
                ),
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ))
            ->add('comment', TextareaType::class, array(
                'label' => 'コメント',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $this->config['ltext_len'])),
                ),
                'attr' => array(
                    'maxlength' => $this->config['ltext_len'],
                ),
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'product_review';
    }
}