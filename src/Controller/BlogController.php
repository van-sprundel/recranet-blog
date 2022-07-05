<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\BlogPostCreateType;
use App\Form\BlogPostEditType;
use App\Form\CommentCreateType;
use App\Repository\BlogPostRepository;
use App\Repository\CommentRepository;
use App\Repository\UserAuthRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private BlogPostRepository $blogPostRepository;
    private CommentRepository $commentRepository;
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
        $this->commentRepository = $entityManager->getRepository(Comment::class);
        $this->logger = $logger;
        $this->security = $security;
    }

    #[Route('/blogs', name: 'blogs')]
    public function index(): Response
    {
        $blogPosts = $this->blogPostRepository->findBy([], ['createdOn' => 'DESC']);
        $blogPosts = array_slice($blogPosts, 0, 5);
        return $this->render('blog/index.html.twig', [
            'blog_posts' => $blogPosts,
        ]);
    }

    #[Route('/my_blog', name: 'my_blog')]
    #[IsGranted('ROLE_USER')]
    public function myBlog(): Response
    {
        $user = $this->getUser();
        $blogPosts = $this->blogPostRepository->findBy(
            ['createdBy' => $user],
            ['createdOn' => 'DESC']
        );

        return $this->render('blog/my_blog.html.twig', [
            'blog_posts' => $blogPosts,
            'user' => $user
        ]);
    }

    #[Route('/blogs/create', name: 'blogCreate')]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request          $request,
        SluggerInterface $slugger,
        MailerInterface  $emailer
    ): Response
    {
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostCreateType::class, $blogPost);
        $form->handleRequest($request);

        /* @var UploadedFile $imageUploadedFile */
        $imageUploadedFile = $form->get('headImage')->getData();
        $extraImageUploadedFile = $form->get('extraImages')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $newBlogpost = $form->getData();

            if ($imageUploadedFile) {
                $newHeadImage = $this->CreateFileName($imageUploadedFile, $slugger);
                $newBlogpost->setHeadImage($newHeadImage);
            }

            if ($extraImageUploadedFile) {
                foreach ($extraImageUploadedFile as $uploadedImage) {
                    if ($uploadedImage != null) {
                        $newImageName = $this->CreateFileName($uploadedImage, $slugger);
                        $newBlogpost->addExtraImage($newImageName);
                    }
                }
            }

            $newBlogpost->setCreatedOn(new \DateTime());
            $user = $this->getUser();

            if ($user) {
                $newBlogpost->setCreatedBy($user);
            }

            $this->entityManager->persist($newBlogpost);
            $this->entityManager->flush();
            $this->sendEmail($emailer, $user, $blogPost);
            return $this->redirect('/my_blog');
        }

        return $this->renderForm('blog/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/blogs/{id}/edit', name: 'blogEdit')]
    #[IsGranted('ROLE_USER')]
    public function edit(
        int              $id,
        Request          $request,
        SluggerInterface $slugger
    ): Response
    {
        $blogPost = $this->blogPostRepository->find($id);
        $form = $this->createForm(BlogPostEditType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newBlogpost = $form->getData();

            $blogPost->setTitle($newBlogpost->getTitle());
            $blogPost->setSubtitle($newBlogpost->getSubtitle());
            $blogPost->setContent($newBlogpost->getContent());

            $this->entityManager->flush();
            return $this->redirect('/blogs/' . $blogPost->getId());
        }

        return $this->renderForm('blog/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/blogs/{id}', name: 'blogById')]
    public function getById(
        int     $id,
        Request $request
    ): Response
    {
        $comment = new Comment();
        $form = $this->createForm(
            CommentCreateType::class,
            $comment,
            ['action' =>
                $this->generateUrl(
                    'app_blog_commentonblog',
                    ['blogId' => $id]
                )
            ]
        );
        $form->handleRequest($request);

        $blogPost = $this->blogPostRepository->find($id);
        if ($blogPost) {
            return $this->render('blog/read.html.twig', [
                'blog_post' => $blogPost,
                'comment_form' => $form->createView()
            ]);
        } else {
            return $this->render('404.html.twig');
        }
    }

    #[Route('/blogs/{id}/delete', name: 'deleteBlogById', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteById(
        int $id
    ): Response
    {
        $blogPost = $this->blogPostRepository->find($id);

        if ($blogPost->getCreatedBy() === $this->getUser()) {
            $this->blogPostRepository->remove($blogPost);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('my_blog');
    }

    #[Route('/blogs/{blogId}/makeComment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function commentOnBlog(
        int     $blogId,
        Request $request
    ): RedirectResponse
    {
        $comment = new Comment();
        $form = $this->createForm(
            CommentCreateType::class,
            $comment,
        );
        $form->handleRequest($request);
        $comment = $form->getData();
        $comment->setCreatedOn(new \DateTime());
        $user = $this->getUser();
        if ($user) {
            $comment->setUser($user);
        }
        $blogPost = $this->blogPostRepository->find($blogId);
        $comment->setBlogPost($blogPost);
        $this->commentRepository->add($comment);
        $this->entityManager->flush();

        return $this->redirectToRoute(
            'blogById',
            ['id' => $blogId]
        );
    }

    public function CreateFileName(
        UploadedFile     $item,
        SluggerInterface $slugger
    ): string
    {
        $originalImageName = pathinfo($item->getClientOriginalName(), PATHINFO_FILENAME);

        $safeImageName = $slugger->slug($originalImageName);
        $newImageName = $safeImageName . '-' . uniqid() . '.' . $item->guessExtension();

        try {
            $item->move(
                $this->getParameter('images_directory'),
                $newImageName
            );
        } catch (FileException $e) {
            echo $e;
        }
        return $newImageName;
    }

    public function sendEmail(MailerInterface $mailer, UserInterface $user, BlogPost $blogPost)
    {
        $link = $this->generateUrl(
            'blogById', [
            'id' => $blogPost->getId()
        ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $email = (new Email())
            ->from('ramones@localhost.com')
            ->to($user->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Blog post uploaded successfully!')
            ->text("Here's the link to your new blog post!" . $link);

        $mailer->send($email);
    }


}

