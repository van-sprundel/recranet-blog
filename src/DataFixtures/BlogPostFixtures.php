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
    private $lorem = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis.";
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
        $blogPost->setContent($this->lorem);
        $blogPost->setSubtitle($this->nameGenerator->getName());
        $blogPost->setCreatedOn(new \DateTime());
        $blogPost->setCreatedBy($this->getReference($userReference));
        $blogPost->setHeadImage('dog-puppy.jpg');

        $manager->persist($blogPost);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
