<?php

namespace App\Controller;

use App\Entity\ShoppingItem;
use App\Form\ShoppingItemType;
use App\Repository\ShoppingItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shopping')]
class ShoppingItemController extends AbstractController
{
    #[Route('/', name: 'app_shopping_item_index', methods: ['GET'])]
    public function index(ShoppingItemRepository $shoppingItemRepository): Response
    {

        $items = $this->isGranted('ROLE_ADMIN') ? $shoppingItemRepository->findAll() : $this->getUser()->getShoppingItems();

        return $this->render('shopping_item/index.html.twig', [
            'shopping_items' => $items,
        ]);
    }

    #[Route('/new', name: 'app_shopping_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $this->checkPermission();

        $shoppingItem = new ShoppingItem();
        $form = $this->createForm(ShoppingItemType::class, $shoppingItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($shoppingItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_shopping_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shopping_item/new.html.twig', [
            'shopping_item' => $shoppingItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_shopping_item_show', methods: ['GET'])]
    public function show(ShoppingItem $shoppingItem): Response
    {
        $this->checkPermission($shoppingItem);
        $this->checkOwnership($shoppingItem);
        return $this->render('shopping_item/show.html.twig', [
            'shopping_item' => $shoppingItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_shopping_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ShoppingItem $shoppingItem, EntityManagerInterface $entityManager): Response
    {
        $this->checkPermission($shoppingItem);
        $this->checkOwnership($shoppingItem);

        $form = $this->createForm(ShoppingItemType::class, $shoppingItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_shopping_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shopping_item/edit.html.twig', [
            'shopping_item' => $shoppingItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_shopping_item_delete', methods: ['POST'])]
    public function delete(Request $request, ShoppingItem $shoppingItem, EntityManagerInterface $entityManager): Response
    {
        $this->checkPermission($shoppingItem);
        $this->checkOwnership($shoppingItem);

        if ($this->isCsrfTokenValid('delete'.$shoppingItem->getId(), $request->request->get('_token'))) {
            $entityManager->remove($shoppingItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_shopping_item_index', [], Response::HTTP_SEE_OTHER);
    }

    private function checkOwnership(ShoppingItem $shoppingItem): void
    {
        if($shoppingItem->getUser() !== $this->getUser()){
            throw $this->createNotFoundException();
        }
    }

    private function checkPermission(){
        if($this->isGranted('ROLE_ADMIN')){
            throw $this->createAccessDeniedException();
        }
    }




}
