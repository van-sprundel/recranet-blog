<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\User;
use App\Form\BlogPostType;
use App\Repository\BlogPostRepository;
use App\Repository\UserAuthRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private BlogPostRepository $blogPostRepository;
    private $security;
    private $logger;

    public function __construct(
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Security               $security
    )
    {
        $this->entityManager = $entityManager;
        $this->blogPostRepository = $entityManager->getRepository(BlogPost::class);
        $this->logger = $logger;
        $this->security = $security;
    }

    #[Route('/blogs', name: 'blogs')]
    public function index(): Response
    {
        $blogPosts = $this->blogPostRepository->findAll();

        return $this->render('blog/index.html.twig', [
            'blog_posts' => $blogPosts,
        ]);
    }

    #[Route('/my_blog', name: 'my_blog')]
    public function myBlog(): Response
    {
        $user = $this->getUser();
        $blogPosts = $this->blogPostRepository->findBy(['createdBy'=> $user]);

        return $this->render('blog/my_blog.html.twig', [
            'blog_posts' => $blogPosts,
            'user'=> $user
        ]);
    }

    #[Route('/blogs/create', name: 'blogCreate')]
    public function create(Request $request, SluggerInterface $slugger): Response
    {
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostType::class, $blogPost);
        $form->handleRequest($request);

        /** @var UploadedFile $imageUploadedFile */
        $imageUploadedFile = $form->get('headImage')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $newBlogpost = $form->getData();

            $originalImageName = pathinfo($imageUploadedFile->getClientOriginalName(),PATHINFO_FILENAME);
            $safeImageName = $slugger->slug($originalImageName);
            $newImageName = $safeImageName.'-'.uniqid().'-'.$imageUploadedFile->guessExtension();

            try {
                $imageUploadedFile->move(
                    $this->getParameter('images_directory'),
                    $newImageName
                );
            } catch (FileException $e) {}

            $newBlogpost->setHeadImage($newImageName);
            $newBlogpost->setCreatedOn(new \DateTime());
            $user = $this->getUser();

            if ($user) {
                $newBlogpost->setCreatedBy($user);
            }

            $this->entityManager->persist($newBlogpost);
            $this->entityManager->flush();
            return $this->redirect('/my_blog');
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
