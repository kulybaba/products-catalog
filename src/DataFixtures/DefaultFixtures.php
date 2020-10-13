<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tag;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DefaultFixtures extends Fixture
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function load(ObjectManager $manager)
    {
        $category1 = new Category();
        $category1->setName('Smart phones');
        $category1->setPosition(1);
        $manager->persist($category1);
        $manager->flush();

        $category2 = new Category();
        $category2->setName('Laptops');
        $category2->setPosition(2);
        $manager->persist($category2);
        $manager->flush();

        $category3 = new Category();
        $category3->setName('Monitors');
        $category3->setPosition(3);
        $manager->persist($category3);
        $manager->flush();

        $category4 = new Category();
        $category4->setName('Peripheral devices');
        $category4->setPosition(4);
        $manager->persist($category4);
        $manager->flush();

        $category5 = new Category();
        $category5->setName('Others');
        $category5->setPosition(5);
        $manager->persist($category5);
        $manager->flush();

        $user = new User();
        $user->setFirstName('User');
        $user->setLastName('User');
        $user->setRoles([User::ROLE_USER]);
        $user->setEmail('user@mail.com');
        $user->setPlainPassword('11111111');
        $user->setApiToken($this->userService->generateApiToken());
        $manager->persist($user);
        $manager->flush();

        $adminManager = new User();
        $adminManager->setFirstName('Admin');
        $adminManager->setLastName('Manager');
        $adminManager->setRoles([User::ROLE_ADMIN_MANAGER]);
        $adminManager->setEmail('manager@mail.com');
        $adminManager->setPlainPassword('11111111');
        $adminManager->setApiToken($this->userService->generateApiToken());
        $adminManager->addCategory($category2);
        $manager->persist($adminManager);
        $manager->flush();

        $superAdmin = new User();
        $superAdmin->setFirstName('Super');
        $superAdmin->setLastName('Admin');
        $superAdmin->setRoles([User::ROLE_SUPER_ADMIN]);
        $superAdmin->setEmail('superadmin@mail.com');
        $superAdmin->setPlainPassword('11111111');
        $superAdmin->setApiToken($this->userService->generateApiToken());
        $manager->persist($superAdmin);
        $manager->flush();

        $adminManager->addCategory($category1);
        $manager->persist($adminManager);
        $manager->flush();

        $tag1 = new Tag();
        $tag1->setText('New');
        $tag1->setPosition(1);
        $manager->persist($tag1);
        $manager->flush();

        $tag2 = new Tag();
        $tag2->setText('ACER');
        $tag2->setPosition(2);
        $manager->persist($tag2);
        $manager->flush();

        $tag3 = new Tag();
        $tag3->setText('Android');
        $tag3->setPosition(3);
        $manager->persist($tag3);
        $manager->flush();

        $product1 = new Product();
        $product1->setName('Acer Swift 3 SF314-54');
        $product1->setDescription('Екран 14" IPS (1920x1080) Full HD, матовий / Intel Core i3-8130U (2.2 - 3.4 ГГц) / RAM 4 ГБ / SSD 128 ГБ / Intel UHD Graphics 620 / без ОД / Wi-Fi / Bluetooth / веб-камера / Linux / 1.45 кг / сріблястий');
        $product1->setColor('#858585');
        $product1->setCount(5);
        $product1->setPrice(25000);
        $product1->setCurrency('UAH');
        $product1->setManager($adminManager);
        $product1->addCategory($category2);
        $product1->addTag($tag2);
        $manager->persist($product1);
        $manager->flush();

        $product1->addTag($tag1);
        $manager->persist($product1);
        $manager->flush();

        $product1 = new Product();
        $product1->setName('Xiaomi Mi 9 6/64GB Ocean Blue');
        $product1->setDescription('Невероятно быстрый процессор Snapdragon 855. Прирост производительности в играх на 20%, прирост производительности процессора на 45%, на 300% более быстрый ИИ. Крупнейшее обновление в истории Qualcomm Snapdragon');
        $product1->setColor('#125de5');
        $product1->setCount(2);
        $product1->setPrice(13999);
        $product1->setCurrency('UAH');
        $product1->setManager($adminManager);
        $product1->addCategory($category1);
        $product1->addTag($tag3);
        $manager->persist($product1);
        $manager->flush();
    }
}
