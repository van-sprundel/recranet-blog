<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nubs\RandomNameGenerator\All;

class BlogPostFixtures extends Fixture implements DependentFixtureInterface
{
    private $nameGenerator;

    public function __construct()
    {
        $this->nameGenerator = All::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->insertItem($manager, UserFixtures::USER_1_REFERENCE);
        $this->insertItem($manager, UserFixtures::USER_1_REFERENCE);
        $this->insertItem($manager, UserFixtures::USER_1_REFERENCE);

        $manager->flush();
    }

    public function insertItem(ObjectManager $manager, string $userReference): void
    {
        $blogPost = new BlogPost();

        $blogPost->setTitle($this->nameGenerator->getName());
        $blogPost->setDescription($this->nameGenerator->getName());
        $blogPost->setCreatedBy($this->getReference($userReference));

        $manager->persist($blogPost);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
