<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nubs\RandomNameGenerator\All;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_1_REFERENCE = 'user-1';
    public const ADMIN_USER_REFERENCE = 'admin-user';
    private $nameGenerator;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->nameGenerator = All::create();
        $this->userPasswordHasher = $userPasswordHasher;
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
        $user->setEmail($reference . "@gmail.com");
        $user->setPassword($this->userPasswordHasher->hashPassword(
            $user,
            $reference
        ));

        $manager->persist($user);
        $this->setReference($reference, $user);
    }
}
