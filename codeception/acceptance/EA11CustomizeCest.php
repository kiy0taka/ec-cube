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
     * @group customize_username
     * @see https://github.com/EC-CUBE/ec-cube/pull/4687
     */
    public function test_login_with_login_id(\AcceptanceTester $I)
    {
        $loginId = 'customer1';
        $Customer = $this->newCustomer('customer1@example.com');
        $Customer->setLoginId($loginId);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = Fixtures::get('entityManager');
        $entityManager->persist($Customer);
        $entityManager->flush();

        $I->loginAsMember($loginId, 'password');
    }

    /**
     * @group customize_username
     * @see https://github.com/EC-CUBE/ec-cube/pull/4687
     */
    public function test_login_fail_with_email(\AcceptanceTester $I)
    {
        $email = 'customer2@example.com';
        $Customer = $this->newCustomer($email);
        $Customer->setLoginId('customer2');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = Fixtures::get('entityManager');
        $entityManager->persist($Customer);
        $entityManager->flush();

        $I->amOnPage('/mypage/login');
        $I->submitForm('#login_mypage', [
            'login_email' => $email,
            'login_pass' => 'password',
        ]);

        $I->see('ログインできませんでした。', 'p.ec-errorMessage');
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
