<?php

namespace App\Controller\GestApp;

use App\Entity\GestApp\Avis;
use App\Form\GestApp\AvisType;
use App\Repository\GestApp\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisController extends AbstractController
{
    /**
     * @Route("/op_admin/gestapp/avis/", name="op_gestapp_avis_index", methods={"GET"})
     */
    public function index(AvisRepository $avisRepository): Response
    {
        return $this->render('gest_app/avis/index.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/avis/new", name="op_gestapp_avis_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($avi);
            $entityManager->flush();

            return $this->redirectToRoute('op_gestapp_avis_index');
        }

        return $this->render('gest_app/avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/avis/{id}", name="op_gestapp_avis_show", methods={"GET"})
     */
    public function show(Avis $avi): Response
    {
        $avis = $this->getDoctrine()->getRepository(Avis::class)->findAll();
        return $this->render('gest_app/avis/show.html.twig', [
            'avis' => $avis,
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/avis/{id}/edit", name="op_gestapp_avis_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Avis $avi): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('op_gestapp_avis_index');
        }

        return $this->render('gest_app/avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/avis/{id}", name="op_gestapp_avis_delete", methods={"POST"})
     */
    public function delete(Request $request, Avis $avi): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avi->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($avi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_gestapp_avis_index');
    }

    /**
     * @Route("/op_admin/gestapp/avis/view", name="op_gestapp_avis_view", methods={"GET"})
     */
    public function view(AvisRepository $avisRepository): Response
    {
        return $this->render('gest_app/avis/view.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }

    /**
     * Suppression d'une ligne index.php
     * @Route("/op_admin/gestapp/avis/del/{id}", name="op_gestapp_avis_suppr", methods={"POST"})
     */
    public function DelEvent(Request $request, Avis $avis) : Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($avis);
        $entityManager->flush();

        $liste = $this->getDoctrine()->getRepository(Avis::class)->findAll();

        return $this->json([
            'code'=> 200,
            'message' => "L'évènenemt a été supprimé",
            'liste' => $this->renderView('gest_app/avis/include/_liste.html.twig', [
                'avis' => $liste
            ]),
        ], 200);
    }
}
