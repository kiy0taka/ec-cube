<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Customize\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\Customer")
 */
trait CustomerTrait
{
    private static $usernameField = 'login_id';

    /**
     * @ORM\Column(name="login_id", type="string", nullable=false, unique=true)
     * @var string
     */
    private $login_id;

    /**
     * @return mixed
     */
    public function getLoginId()
    {
        return $this->login_id;
    }

    /**
     * @param mixed $login_id
     */
    public function setLoginId($login_id): void
    {
        $this->login_id = $login_id;
    }
}
