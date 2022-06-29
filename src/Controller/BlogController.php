<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private BlogPostRepository $blogPostRepository;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->blogPostRepository = $doctrine->getRepository(BlogPost::class);
    }

    #[Route('/blogs', name: 'blogs')]
    public function index(): Response
    {
        $blogPosts = $this->blogPostRepository->findAll();


        return $this->render('blog/index.html.twig', [
            'blog_posts' => $blogPosts,
        ]);
    }

    #[Route('/blogs/{id}', name: 'blogByIs')]
    public function getById(int $id): Response
    {
        $blogPost = $this->blogPostRepository->find($id);

        dd($blogPost);

       if ($blogPost) {
           return $this->render('blog/read.html.twig',[
           'blog_post' => $blogPost,
        ]);
       } else {
           return $this->render('404.html.twig');
       }
    }
}
