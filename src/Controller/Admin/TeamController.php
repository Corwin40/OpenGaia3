<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Team;
use App\Form\Admin\TeamType;
use App\Repository\Admin\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/team')]
class TeamController extends AbstractController
{
    #[Route('/', name: 'admin_team_index', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('admin/team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('/listAllteams', name: 'op_admin_team_listallteams', methods: ['GET'])]
    public function listAllTeams(TeamRepository $teamRepository): Response
    {
        return $this->render('admin/team/all.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('/{idStruct}', name: 'op_admin_team_listTeam', methods: ['GET'])]
    public function listTeam(TeamRepository $teamRepository, $idStruct): Response
    {
        return $this->render('admin/team/liststruct.html.twig', [
            'teams' => $teamRepository->listStruct($idStruct),
        ]);
    }

    #[Route('/new', name: 'op_admin_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('admin_team_index');
        }

        return $this->render('admin/team/new.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'op_admin_team_show', methods: ['GET'])]
    public function show(Team $team): Response
    {
        return $this->render('admin/team/show.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_admin_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Team $team): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_team_index');
        }

        return $this->render('admin/team/edit.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'op_admin_team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_team_index');
    }
}
