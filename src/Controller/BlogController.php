<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Form\Type\BlogPostType;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private BlogPostRepository $blogPostRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->blogPostRepository = $entityManager->getRepository(BlogPost::class);
    }

    #[Route('/blogs', name: 'blogs')]
    public function index(): Response
    {
        $blogPosts = $this->blogPostRepository->findAll();


        return $this->render('blog/index.html.twig', [
            'blog_posts' => $blogPosts,
        ]);
    }

    #[Route('/blogs/create', name: 'blogCreate')]
    public function create(Request $request): Response
    {
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newBlogpost = $form->getData();
            $newBlogpost->setCreatedOn(new \DateTime());
//            $newBlogpost->setCreatedBy();

            $this->entityManager->persist($newBlogpost);
            $this->entityManager->flush();
            return $this->redirect('/blogs');
        }

        return $this->renderForm('blog/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/blogs/{id}', name: 'blogByIs')]
    public function getById(int $id): Response
    {
        $blogPost = $this->blogPostRepository->find($id);
        if ($blogPost) {
            return $this->render('blog/read.html.twig', [
                'blog_post' => $blogPost,
            ]);
        } else {
            return $this->render('404.html.twig');
        }
    }
}
