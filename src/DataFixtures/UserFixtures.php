<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nubs\RandomNameGenerator\All;

class UserFixtures extends Fixture
{
    public const USER_1_REFERENCE = 'user-1';
    public const ADMIN_USER_REFERENCE = 'admin-user';
    private $nameGenerator;

    public function __construct()
    {
        $this->nameGenerator = All::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->insertItem($manager, self::USER_1_REFERENCE);

        $manager->flush();
    }

    public function insertItem(ObjectManager $manager, string $reference): void
    {
        $user = new User();

        $user->setUsername($this->nameGenerator->getName());
        $user->setFullName($this->nameGenerator->getName());
        $user->setCreatedOn(new \DateTime());

        $manager->persist($user);
        $this->setReference($reference, $user);
    }
}
