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

namespace Acme\Controller;

use Acme\Entity\ProductReview;
use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Product;
use Form\Type\ProductReviewType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service=Acme\Controller\ProductReviewController::class)
 */
class ProductReviewController extends AbstractController
{
    /**
     * @var EntityManager
     * @Inject("orm.em")
     */
    private $entityManager;

    /**
     * @Route(path="/products/review/{id}", name="plugin_product_review")
     * @Template("Product/review.twig")
     */
    public function index(Application $app, Request $request, Product $Product)
    {
        $ProductReview = new ProductReview();
        $builder = $this->formFactory->createBuilder(ProductReviewType::class, $ProductReview);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ProductReview->setProduct($Product);
            $this->entityManager->persist($ProductReview);
            $this->entityManager->flush($ProductReview);

            return $app->redirect($app->path('plugin_product_review_complete', ['id' => $Product->getId()]));
        }

        return [
            "Product" => $Product,
            "form" => $form->createView()
        ];
    }

    /**
     * @Route(path="/products/review/complete/{id}", name="plugin_product_review_complete")
     * @Template("Product/review_complete.twig")
     */
    public function complete($id)
    {
        return ['id' => $id];
    }
}