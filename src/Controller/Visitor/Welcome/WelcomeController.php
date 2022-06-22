<?php

namespace App\Controller\Visitor\Welcome;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'visitor.welcome.index')]
    public function index(): Response
    {
        return $this->render('page/visitor/welcome/index.html.twig');
    }

    #[Route('/post', name: 'visitor.welcome.post')]
    public function post(PostRepository $postRepository): Response
    {   
        // if ( $post->getIsPublished)
        $posts = $postRepository->findAll();
        return $this->render('page/visitor/welcome/post.html.twig', compact('posts'));
    }

    #[Route('/post/{id<\d+>}', name: 'visitor.welcome.show', methods: ['GET'])]
    public function show(Post $posts): Response
    {   
        if ( $posts->getIsPublished() == true )
        {
            return $this->render('page/visitor/welcome/show.html.twig', compact('posts'));
        }
        
    }
}
