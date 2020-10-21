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

use Codeception\Util\Fixtures;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\Pref;
use Eccube\Repository\CustomerRepository;
use Eccube\Security\Core\Encoder\PasswordEncoder;

class EA11CustomizeCest
{
    /**
     * @see https://github.com/EC-CUBE/ec-cube/pull/4687
     */
    public function test_username(\AcceptanceTester $I)
    {
        $Customer1 = $this->newCustomer('customer1@example.com');
        $Customer1->setLoginId('customer1');
        $Customer2 = $this->newCustomer('customer2@example.com');
        $Customer2->setLoginId('customer2');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = Fixtures::get('entityManager');
        $entityManager->persist($Customer1);
        $entityManager->persist($Customer2);
        $entityManager->flush();

        $I->loginAsMember('customer1', 'password');
    }

    private function newCustomer($email)
    {
        $Customer = new Customer();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = Fixtures::get('entityManager');
        /** @var CustomerStatus $Status */
        $Status = $entityManager->find(CustomerStatus::class, CustomerStatus::REGULAR);
        /** @var Pref $Pref */
        $Pref = $entityManager->find(Pref::class, 1);
        /** @var CustomerRepository $customerRepository */
        $customerRepository = $entityManager->getRepository(Customer::class);

        $getService = Fixtures::get('getService');
        $passwordEncoder = $getService(PasswordEncoder::class);
        $salt = $passwordEncoder->createSalt();
        $password = $passwordEncoder->encodePassword('password', $salt);
        $Customer
            ->setName01('test')
            ->setName02('test')
            ->setKana01('テスト')
            ->setKana02('テスト')
            ->setEmail($email)
            ->setPostalcode('5300001')
            ->setPref($Pref)
            ->setAddr01('addr01')
            ->setAddr02('addr02')
            ->setPhoneNumber('0123456789')
            ->setPassword($password)
            ->setSalt($salt)
            ->setSecretKey($customerRepository->getUniqueSecretKey())
            ->setStatus($Status)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());

        return $Customer;
    }
}
