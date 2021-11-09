<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Parameter;
use App\Entity\Admin\Parrainage;
use App\Form\Admin\ParrainageType;
use App\Repository\Admin\ParrainageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;


class ParrainageController extends AbstractController
{
    /**
     * @Route("/opadmin/parrainage", name="op_admin_parrainage_index", methods={"GET"})
     */
    public function index(ParrainageRepository $parrainageRepository): Response
    {
        return $this->render('admin/parrainage/index.html.twig', [
            'parrainages' => $parrainageRepository->findAll(),
        ]);
    }
    /**
     * @Route("/opadmin/parrainage/add", name="op_admin_parrainage_add", methods={"POST"})
     */
    public function add(Request $request, MailerInterface $mailer)
    {
        // récupération des données
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        // Hydratation de l'objet et persistance en BDD
        $parrainage = new Parrainage();
        $parrainage->setAuthor($user);
        $parrainage->setParrainageFirstname($data['parrainageFirstname']);
        $parrainage->setParrainageLastname($data['parrainageLastname']);
        $parrainage->setParrainageEmail($data['parrainageEmail']);
        $parrainage->setPhoneDesk($data['phoneDesk']);
        $parrainage->setPhoneGsm($data['phoneGsm']);
        $parrainage->setNameSociety($data['nameSociety']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($parrainage);
        $entityManager->flush();

        $parameter = $this->getDoctrine()->getRepository(Parameter::class)->find(1);
        $webmaster = $parameter->getAdminEmail();

        // Envoyer un email au Just
        $email = (new TemplatedEmail())
            ->from('postmaster@openpixl.fr')
            ->to($webmaster)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('JUSTàFaire - Nouvelle Invitation pour une adhésion')
            //->text('Sending emails is fun again!')
            ->htmlTemplate('email/newInvitationWebMaster.html.twig')
            ->context([
                'author' => $parrainage->getAuthor(),
                'nameSociety'=> $parrainage->getNameSociety(),
                'firstName'=> $parrainage->getParrainageFirstname(),
                'lastName' => $parrainage->getParrainageLastname(),
                'phoneGSM' => $parrainage->getPhoneGsm(),
                'phoneDesk' => $parrainage->getPhoneDesk(),
                'parrainageEmail' => $parrainage->getParrainageEmail()
            ]);
        $mailer->send($email);

        return $this->json([
            'code'=> 200,
            'message' => "Votre invité a été inscrit, l'équipe du JUST va la contacter ,pour sa participation au prochain RDV."
        ], 200);


    }

    /**
     * @Route("/opadmin/parrainage//new", name="op_admin_parrainage_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $parrainage = new Parrainage();
        $form = $this->createForm(ParrainageType::class, $parrainage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($parrainage);
            $entityManager->flush();

            return $this->redirectToRoute('op_admin_parrainage_index');
        }

        return $this->render('admin/parrainage/new.html.twig', [
            'parrainage' => $parrainage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/opadmin/parrainage//{id}", name="op_op_admin_parrainage_show", methods={"GET"})
     */
    public function show(Parrainage $parrainage): Response
    {
        return $this->render('admin/parrainage/show.html.twig', [
            'parrainage' => $parrainage,
        ]);
    }

    /**
     * @Route("/opadmin/parrainage//{id}/edit", name="op_admin_parrainage_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Parrainage $parrainage): Response
    {
        $form = $this->createForm(ParrainageType::class, $parrainage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('op_admin_parrainage_index');
        }

        return $this->render('admin/parrainage/edit.html.twig', [
            'parrainage' => $parrainage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/opadmin/parrainage//{id}", name="op_admin_parrainage_delete", methods={"POST"})
     */
    public function delete(Request $request, Parrainage $parrainage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parrainage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($parrainage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_admin_parrainage_index');
    }

    /**
     * @Route("/opadmin/parrainage/firstmeeting/{id}", name="op_admin_parrainage_firstmeeting", methods={"POST"})
     */
    public function firstMeeting(Request $request, MailerInterface $mailer, Parrainage $parrainage)
    {
        $parrainage->setFirstMeeting(New \DateTime());
        $this->getDoctrine()->getManager()->flush();

        $parrainages = $this->getDoctrine()->getRepository(Parrainage::class)->findAll();

        return $this->json([
            'code'=> 200,
            'message' => "Votre invité a été inscrit pour sa première participation.",
            'liste' => $this->renderView('admin/parrainage/include/_liste.html.twig', [
                'parrainages' => $parrainages,
            ]),
        ], 200);
    }

    /**
     * @Route("/opadmin/parrainage/secondmeeting/{id}", name="op_admin_parrainage_secondmeeting", methods={"POST"})
     */
    public function secondMeeting(Request $request, MailerInterface $mailer, Parrainage $parrainage)
    {
        $parrainage->setSecondMeeting(New \DateTime());
        $this->getDoctrine()->getManager()->flush();

        $parrainages = $this->getDoctrine()->getRepository(Parrainage::class)->findAll();

        return $this->json([
            'code'=> 200,
            'message' => "Votre invité est inscrit pour sa seconde participation.",
            'liste' => $this->renderView('admin/parrainage/include/_liste.html.twig', [
                'parrainages' => $parrainages,
            ]),
        ], 200);
    }

    /**
     * @Route("/opadmin/parrainage/iscotisation/{id}", name="op_admin_parrainage_iscotisation", methods={"GET","POST"})
     */
    public function isCotisation(Request $request, EntityManagerInterface $em, Parrainage $parrainage)
    {
        $isCotisation = $parrainage->getIsCotisation();
        // Si l'invitaté a déjà contisé, on dépublie
        if($isCotisation == true){
            $parrainage->setIsCotisation(0);
            $em->flush();
            return $this->json(['code'=> 200, 'message' => "L'utilisateur n'accède plus à l'administration"], 200);
        }
        $parrainage->setIsCotisation(1);
        $this->getDoctrine()->getManager()->flush();

        $parrainages = $this->getDoctrine()->getRepository(Parrainage::class)->findAll();

        return $this->json([
            'code'=> 200,
            'message' => "Votre invité s'est inscrit au JUSTàFAIRE.",
        ], 200);
    }
}
