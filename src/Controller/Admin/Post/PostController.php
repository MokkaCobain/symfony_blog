<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/admin/post', name: 'admin.post.index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        return $this->render('page/admin/post/index.html.twig', compact('posts'));
    }

    #[Route('/admin/post/create', name: 'admin.post.create', methods: ['GET', 'POST'])]
    public function create(Request $request, PostRepository $postRepository, CategoryRepository $categoryRepository): Response
    {
        if(! $categoryRepository->findAll())
        {
            $this->addFlash('', "Vous devez créer au moins une catégorie avant de rédiger un article.");
            return $this->redirectToRoute('admin.category.index');
        }
        
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // Récupérer l'auteur via la session
            $post->setAuthor($this->getUser());

            $postRepository->add($post, true);

            $this->addFlash('success', 'L\'article a bien été ajouté !');

            return $this->redirectToRoute('admin.post.index');
        }

        return $this->renderForm('page/admin/post/create.html.twig', compact('form'));
    }

    #[Route('/admin/post/{id<\d+>}', name: 'admin.post.show', methods: ['GET'])]
    public function show(Post $post, PostRepository $postRepository): Response
    {
        return $this->render('page/admin/post/show.html.twig', compact('post'));
    }

    #[Route('/admin/post/edit/{id<\d+>}', name: 'admin.post.edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $request, PostRepository $postRepository): Response
    {
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // Récupérer l'auteur via la session
            $post->setAuthor($this->getUser());

            $postRepository->add($post, true);

            $this->addFlash('success', 'L\'article  "' . $post->getTitle() .  '" a bien été modifié !');

            return $this->redirectToRoute('admin.post.index');
        }

        return $this->renderForm('page/admin/post/edit.html.twig', compact('form', 'post'));
    }

    #[Route('/admin/post/publish/{id<\d+>}', name: 'admin.post.published', methods: array('POST'))]
    public function publish(Post $post, PostRepository $postRepository): Response
    {

        if ($post->getIsPublished() == false)
        {
            $post->setIsPublished(true);
            $post->setPublishedAt(new \DateTimeImmutable('now'));
            $postRepository->add($post, true);
            

            $this->addFlash('success', 'L\'article "' . $post->getTitle() .  '" a été publié !');

           
        }
        else 
        {
            $post->setIsPublished(false);
            $post->setPublishedAt(null);
            $postRepository->add($post, true);

            
            $this->addFlash('warning', 'L\'article "' . $post->getTitle() .  '" a été retiré de la publication !');
        }

        return $this->redirectToRoute('admin.post.index');
    }

    #[Route('/admin/post/delete/{id<\d+>}', name: 'admin.post.delete')]
    public function delete(Post $post, Request $request, PostRepository $postRepository): Response
    {
        if ( $this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token')) )
        {
            $postRepository->remove($post, true);
            $this->addFlash('warning', 'L\'article "' . $post->getTitle() . '" a bien été supprimé !');

        }

        return $this->redirectToRoute('admin.post.index');
    }

}
