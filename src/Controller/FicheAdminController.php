<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Form\Fiche1Type;
use App\Repository\FicheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fiche/admin')]
class FicheAdminController extends AbstractController
{
    #[Route('/', name: 'app_fiche_admin_index', methods: ['GET'])]
    public function index(FicheRepository $ficheRepository): Response
    {
        return $this->render('fiche_admin/index.html.twig', [
            'fiches' => $ficheRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_fiche_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FicheRepository $ficheRepository): Response
    {
        $fiche = new Fiche();
        $form = $this->createForm(Fiche1Type::class, $fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ficheRepository->save($fiche, true);

            return $this->redirectToRoute('app_fiche_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fiche_admin/new.html.twig', [
            'fiche' => $fiche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fiche_admin_show', methods: ['GET'])]
    public function show(Fiche $fiche): Response
    {
        return $this->render('fiche_admin/show.html.twig', [
            'fiche' => $fiche,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fiche_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fiche $fiche, FicheRepository $ficheRepository): Response
    {
        $form = $this->createForm(Fiche1Type::class, $fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ficheRepository->save($fiche, true);

            return $this->redirectToRoute('app_fiche_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fiche_admin/edit.html.twig', [
            'fiche' => $fiche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fiche_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Fiche $fiche, FicheRepository $ficheRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fiche->getId(), $request->request->get('_token'))) {
            $ficheRepository->remove($fiche, true);
        }

        return $this->redirectToRoute('app_fiche_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
