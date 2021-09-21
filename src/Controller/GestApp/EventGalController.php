<?php

namespace App\Controller\GestApp;

use App\Entity\GestApp\EventGal;
use App\Form\GestApp\EventGalType;
use App\Repository\GestApp\EventGalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gest/app/eventgal")
 */
class EventGalController extends AbstractController
{
    /**
     * @Route("/gest/app/eventgal/", name="op_gestapp_eventgal_index", methods={"GET"})
     */
    public function index(EventGalRepository $eventGalRepository): Response
    {

        return $this->render('gest_app/event_gal/index.html.twig', [
            'event_gals' => $eventGalRepository->findAll(),
        ]);
    }

    /**
     * @Route("/gest/app/eventgalpublish/{idevent}", name="op_gestapp_eventgal_eventgalpublish", methods={"GET"})
     */
    public function EventGalPublish($idevent, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $this->getDoctrine()->getRepository(EventGal::class)->EventGalPublish($idevent);
        $eventGal = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            20
        );
        return $this->render('gest_app/event_gal/EventGalPublish.html.twig', [
            'event_gals' => $eventGal,
        ]);
    }

    /**
     * @Route("/gest/app/eventgal/{id}/{orientation}", name="op_gestapp_eventgal_orientation", methods={"POST"})
     */
    public function EventGalOrientation(EventGal $eventGal, $orientation): Response
    {
        $eventGal->setOrientation($orientation);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'code'=> 200,
            'message' => "L'orientation a été validée",
        ], 200);
    }

    /**
     * @Route("/gest/app/eventgal/new", name="op_gestapp_eventgal_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $eventGal = new EventGal();
        $form = $this->createForm(EventGalType::class, $eventGal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eventGal);
            $entityManager->flush();

            return $this->redirectToRoute('op_gestapp_eventgal_new');
        }

        return $this->render('gest_app/event_gal/new.html.twig', [
            'event_gal' => $eventGal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gest/app/eventgal/{id}", name="op_gestapp_eventgal_show", methods={"GET"})
     */
    public function show(EventGal $eventGal): Response
    {
        return $this->render('gest_app/event_gal/show.html.twig', [
            'event_gal' => $eventGal,
        ]);
    }

    /**
     * @Route("/gest/app/eventgal/{id}/edit", name="op_gestapp_eventgal_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EventGal $eventGal): Response
    {
        $form = $this->createForm(EventGalType::class, $eventGal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gest_app_event_gal_index');
        }

        return $this->render('gest_app/event_gal/edit.html.twig', [
            'event_gal' => $eventGal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gest/app/eventgal/{id}", name="op_gestapp_eventgal_delete", methods={"POST"})
     */
    public function delete(Request $request, EventGal $eventGal): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventGal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eventGal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gest_app_event_gal_index');
    }

    /**
     * Affiche le contenu de la galerie dans l'admin selon un event.
     * @Route("/opadmin/gestapp/showGalByEvent/{event}", name="op_admin_eventgal_showgalbyevent", methods={"GET"})
     */
    public function showGalByEvent(EntityManagerInterface $em, $event)
    {
        $galeries = $em->getRepository(EventGal::class)->findBy(array('event'=>$event));

        return $this->render('gest_app/event_gal/showgalbyevent.html.twig',[
            'galeries' => $galeries
        ]);
    }
}
