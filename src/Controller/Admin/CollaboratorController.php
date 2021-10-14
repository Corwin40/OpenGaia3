<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Collaborator;
use App\Entity\Admin\Structure;
use App\Form\Admin\CollaboratorType;
use App\Repository\Admin\CollaboratorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CollaboratorController extends AbstractController
{
    /**
     * @Route("/opadmin/collaborator/", name="op_admin_collaborator_index", methods={"GET"})
     */
    public function index(CollaboratorRepository $collaboratorRepository): Response
    {
        return $this->render('admin/collaborator/index.html.twig', [
            'collaborators' => $collaboratorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/opadmin/collaborator/new/{idstructure}", name="op_admin_collaborator_new", methods={"GET","POST"})
     */
    public function new(Request $request, $idstructure): Response
    {
        $structure = $this->getDoctrine()->getRepository(Structure::class)->find($idstructure);
        $collaborator = new Collaborator();
        $collaborator->setStructure($structure);
        $form = $this->createForm(CollaboratorType::class, $collaborator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($collaborator);
            $entityManager->flush();

            return $this->redirectToRoute('op_admin_structure_index');
        }

        return $this->render('admin/collaborator/new.html.twig', [
            'collaborator' => $collaborator,
            'idstructure' => $idstructure,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/opadmin/collaborator/{id}", name="op_admin_collaborator_show", methods={"GET"})
     */
    public function show(Collaborator $collaborator): Response
    {
        return $this->render('admin/collaborator/show.html.twig', [
            'collaborator' => $collaborator,
        ]);
    }

    /**
     * @Route("/opadmin/collaborator/{id}/edit", name="op_admin_collaborator_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Collaborator $collaborator): Response
    {
        $form = $this->createForm(CollaboratorType::class, $collaborator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('op_admin_structure_index');
        }

        return $this->render('admin/collaborator/edit.html.twig', [
            'collaborator' => $collaborator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/opadmin/collaborator/{id}", name="op_admin_collaborator_delete", methods={"POST"})
     */
    public function delete(Request $request, Collaborator $collaborator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collaborator->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($collaborator);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_collaborator_index');
    }
}
