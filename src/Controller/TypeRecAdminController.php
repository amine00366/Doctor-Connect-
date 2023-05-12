<?php

namespace App\Controller;

use App\Entity\TypeReclamation;
use App\Form\TypeReclamation1Type;
use App\Repository\TypeReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/type/rec/admin')]
class TypeRecAdminController extends AbstractController
{
    #[Route('/', name: 'app_type_rec_admin_index', methods: ['GET'])]
    public function index(TypeReclamationRepository $typeReclamationRepository): Response
    {
        return $this->render('type_rec_admin/index.html.twig', [
            'type_reclamations' => $typeReclamationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_rec_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TypeReclamationRepository $typeReclamationRepository): Response
    {
        $typeReclamation = new TypeReclamation();
        $form = $this->createForm(TypeReclamation1Type::class, $typeReclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeReclamationRepository->save($typeReclamation, true);

            return $this->redirectToRoute('app_type_rec_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_rec_admin/new.html.twig', [
            'type_reclamation' => $typeReclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_rec_admin_show', methods: ['GET'])]
    public function show(TypeReclamation $typeReclamation): Response
    {
        return $this->render('type_rec_admin/show.html.twig', [
            'type_reclamation' => $typeReclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_rec_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeReclamation $typeReclamation, TypeReclamationRepository $typeReclamationRepository): Response
    {
        $form = $this->createForm(TypeReclamation1Type::class, $typeReclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeReclamationRepository->save($typeReclamation, true);

            return $this->redirectToRoute('app_type_rec_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_rec_admin/edit.html.twig', [
            'type_reclamation' => $typeReclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_rec_admin_delete', methods: ['POST'])]
    public function delete(Request $request, TypeReclamation $typeReclamation, TypeReclamationRepository $typeReclamationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeReclamation->getId(), $request->request->get('_token'))) {
            $typeReclamationRepository->remove($typeReclamation, true);
        }

        return $this->redirectToRoute('app_type_rec_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
