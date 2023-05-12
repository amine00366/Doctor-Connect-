<?php

namespace App\Controller;

use App\Entity\Ordonnance;
use App\Form\Ordonnance1Type;
use App\Repository\OrdonnanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ordonnanceadmin')]
class OrdonnanceadminController extends AbstractController
{
    #[Route('/', name: 'app_ordonnanceadmin_index', methods: ['GET'])]
    public function index(OrdonnanceRepository $ordonnanceRepository): Response
    {
        return $this->render('ordonnanceadmin/index.html.twig', [
            'ordonnances' => $ordonnanceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ordonnanceadmin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OrdonnanceRepository $ordonnanceRepository): Response
    {
        $ordonnance = new Ordonnance();
        $form = $this->createForm(Ordonnance1Type::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ordonnanceRepository->save($ordonnance, true);

            return $this->redirectToRoute('app_ordonnanceadmin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ordonnanceadmin/new.html.twig', [
            'ordonnance' => $ordonnance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ordonnanceadmin_show', methods: ['GET'])]
    public function show(Ordonnance $ordonnance): Response
    {
        return $this->render('ordonnanceadmin/show.html.twig', [
            'ordonnance' => $ordonnance,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ordonnanceadmin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ordonnance $ordonnance, OrdonnanceRepository $ordonnanceRepository): Response
    {
        $form = $this->createForm(Ordonnance1Type::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ordonnanceRepository->save($ordonnance, true);

            return $this->redirectToRoute('app_ordonnanceadmin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ordonnanceadmin/edit.html.twig', [
            'ordonnance' => $ordonnance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ordonnanceadmin_delete', methods: ['POST'])]
    public function delete(Request $request, Ordonnance $ordonnance, OrdonnanceRepository $ordonnanceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ordonnance->getId(), $request->request->get('_token'))) {
            $ordonnanceRepository->remove($ordonnance, true);
        }

        return $this->redirectToRoute('app_ordonnanceadmin_index', [], Response::HTTP_SEE_OTHER);
    }
}
