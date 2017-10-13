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

namespace Acme\Entity;


use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Product;

/**
 * @ORM\Table(name="dtb_product_review")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\ProductReviewRepository")
 */
class ProductReview extends AbstractEntity
{
    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $reviewer_name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $comment;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $recommend_level;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="\Eccube\Entity\Product", inversedBy="ProductReviews")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     */
    private $Product;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getReviewerName()
    {
        return $this->reviewer_name;
    }

    /**
     * @param string $reviewer_name
     */
    public function setReviewerName($reviewer_name)
    {
        $this->reviewer_name = $reviewer_name;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getRecommendLevel()
    {
        return $this->recommend_level;
    }

    /**
     * @param int $recommend_level
     */
    public function setRecommendLevel($recommend_level)
    {
        $this->recommend_level = $recommend_level;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * @param Product $Product
     */
    public function setProduct($Product)
    {
        $this->Product = $Product;
    }
}