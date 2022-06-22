<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'admin.category.index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('page/admin/category/index.html.twig', compact('categories'));
    }

    #[Route('/admin/category/create', name: 'admin.category.create')]
    public function create(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $categoryRepository->add($category, true);
            $this->addFlash('success', 'La catégorie a bien été ajoutée !');

            return $this->redirectToRoute('admin.category.index');
        }

        return $this->renderForm('page/admin/category/create.html.twig', compact('form'));
    }
    
    #[Route('/admin/category/edit/{id<\d+>}', name: 'admin.category.edit')]
    public function edit(Category $category, Request $request, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $categoryRepository->add($category, true);
            $this->addFlash('success', 'La catégorie a bien été modifiée !');

            return $this->redirectToRoute('admin.category.index');
        }

        return $this->renderForm('page/admin/category/edit.html.twig', compact('form'));
    }
    #[Route('/admin/category/delete/{id<\d+>}', name: 'admin.category.delete')]
    public function delete(Category $category, Request $request, CategoryRepository $categoryRepository): Response
    {
        if ( $this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token')) )
        {
            $categoryRepository->remove($category, true);
            $this->addFlash('warning', 'La catégorie "' . $category->getName() . '" a bien été supprimée !');

        }
        return $this->redirectToRoute('admin.category.index');
    }

}
