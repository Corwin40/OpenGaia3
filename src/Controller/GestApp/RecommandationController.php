<?php

namespace App\Controller\GestApp;

use App\Entity\Admin\Parameter;
use App\Entity\GestApp\Recommandation;
use App\Form\GestApp\RecommandationType;
use App\Repository\GestApp\RecommandationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;


class RecommandationController extends AbstractController
{
    /**
     * @Route("/op_admin/gestapp/recommandation/", name="op_gestapp_recommandation_index", methods={"GET"})
     */
    public function index(RecommandationRepository $recommandationRepository): Response
    {
        $user = $this->getUser();
        $role = $user->getType();

        if ($role == 'admin')
        {
            $receipts = $this->getDoctrine()->getRepository(Recommandation::class)->findByReceiptRead();
            $sends = $this->getDoctrine()->getRepository(Recommandation::class)->findBySendRead();

            return $this->render('gest_app/recommandation/index.html.twig', [
                'receipts' => $receipts,
                'sends' => $sends
            ]);
        }
        else
        {
            $receipts = $this->getDoctrine()->getRepository(Recommandation::class)->findByUserReceiptRead($user);
            $sends = $this->getDoctrine()->getRepository(Recommandation::class)->findByUserSendRead($user);

            return $this->render('gest_app/recommandation/index.html.twig', [
                'receipts' => $receipts,
                'sends' => $sends
            ]);
        }
        $receipts = $this->getDoctrine()->getRepository(Recommandation::class)->findByUserReceiptRead($user);
        $sends = $this->getDoctrine()->getRepository(Recommandation::class)->findByUserSendRead($user);

        return $this->render('gest_app/recommandation/index.html.twig', [
            'receipts' => $receipts,
            'sends' => $sends
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/recommandation/reload", name="op_gestapp_recommandation_indexreload", methods={"GET"})
     */
    public function indexReload(RecommandationRepository $recommandationRepository): Response
    {
        $user = $this->getUser();
        $recommandations = $this->getDoctrine()->getRepository(Recommandation::class)->findByUser($user);
        return $this->render('gest_app/recommandation/reload.html.twig', [
            'recommandations' => $recommandations,
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/recommandation/new", name="op_gestapp_recommandation_new", methods={"GET","POST"})
     */
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        $recommandation = new Recommandation();
        $form = $this->createForm(RecommandationType::class, $recommandation);
        $recommandation->setRecoState('noRead');
        $recommandation->setAuthor($user);
        $recommandation->setIsFirstView(1);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recommandation);
            $entityManager->flush();

            // récupération des données pour la génération des mails à envoyer
            $author = $recommandation->getAuthor();
            $membre = $recommandation->getMember();
            $structure = $membre->getStructure();
            $emailsupervisor = $structure->getEmailStruct();
            $firstNameDesti = $membre->getFirstName();
            $lastNameDesti = $membre->getLastName();
            $email = $membre->getEmail();

            $titleRecom = $recommandation->getName();

            // Envoi du mail de nouvelle recomandation au membre recommandé
            $email = (new TemplatedEmail())
                ->from('postmaster@openpixl.fr')
                ->to($email)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject("JUST à FAIRE - une nouvelle recommandation vous attend dans l'espace JUST.")
                //->text('Sending emails is fun again!')
                ->htmlTemplate('email/newRecommandation.html.twig')
                ->context([
                    'author' => $author,
                    'titleRecom' => $titleRecom,
                    'prenomDestin' => $firstNameDesti,
                    'nomDestin' => $lastNameDesti,
                ]);
            $mailer->send($email);

            // Envoi du mail de nouvelle recomandation à la structure recommandée
            $email = (new TemplatedEmail())
                ->from('postmaster@openpixl.fr')
                ->to($emailsupervisor)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('JUST à FAIRE - une nouvelle recommandation a été posté.')
                //->text('Sending emails is fun again!')
                ->htmlTemplate('email/newRecommandation.html.twig')
                ->context([
                    'author' => $author,
                    'titleRecom' => $titleRecom,
                    'prenomDestin' => $firstNameDesti,
                    'nomDestin' => $lastNameDesti,
                ]);
            $mailer->send($email);

            // Envoi du mail de nouvelle recomandation pour JUST
            $email = (new TemplatedEmail())
                ->from('postmaster@openpixl.fr')
                ->to('contact@justafaire.fr')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('JUST à FAIRE - une nouvelle recommandation a été émise depuis le site')
                //->text('Sending emails is fun again!')
                ->htmlTemplate('email/newRecommandationWebMaster.html.twig')
                ->context([
                    'author' => $author,
                    'titleRecom' => $titleRecom,
                    'prenomDestin' => $firstNameDesti,
                    'nomDestin' => $lastNameDesti,
                ]);
            $mailer->send($email);

            return $this->redirectToRoute('op_gestapp_recommandation_index');
        }

        return $this->render('gest_app/recommandation/new.html.twig', [
            'recommandation' => $recommandation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/recommandation/new", name="op_gestapp_recommandation_new", methods={"GET","POST"})
     */
    public function new_admin(Request $request, MailerInterface $mailer): Response
    {
        // récupération de l'adresse de la structure
        $parameter = $this->getDoctrine()->getRepository(Parameter::class)->find(1);
        //author

        $recommandation = new Recommandation();
        $form = $this->createForm(RecommandationType::class, $recommandation);
        $recommandation->setRecoState('noRead');
        $recommandation->setAuthor($user);
        $recommandation->setIsFirstView(1);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recommandation);
            $entityManager->flush();

            // récupération des données pour la génération des mails à envoyer
            $author = $recommandation->getAuthor();
            $membre = $recommandation->getMember();
            $structure = $membre->getStructure();
            $emailsupervisor = $structure->getEmailStruct();
            $firstNameDesti = $membre->getFirstName();
            $lastNameDesti = $membre->getLastName();
            $email = $membre->getEmail();

            $titleRecom = $recommandation->getName();

            // Envoi du mail de nouvelle recomandation au membre recommandé
            $email = (new TemplatedEmail())
                ->from('postmaster@openpixl.fr')
                ->to($email)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject("JUST à FAIRE - une nouvelle recommandation vous attend dans l'espace JUST.")
                //->text('Sending emails is fun again!')
                ->htmlTemplate('email/newRecommandation.html.twig')
                ->context([
                    'author' => $author,
                    'titleRecom' => $titleRecom,
                    'prenomDestin' => $firstNameDesti,
                    'nomDestin' => $lastNameDesti,
                ]);
            $mailer->send($email);

            // Envoi du mail de nouvelle recomandation à la structure recommandée
            $email = (new TemplatedEmail())
                ->from('postmaster@openpixl.fr')
                ->to($emailsupervisor)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('JUST à FAIRE - une nouvelle recommandation a été posté.')
                //->text('Sending emails is fun again!')
                ->htmlTemplate('email/newRecommandation.html.twig')
                ->context([
                    'author' => $author,
                    'titleRecom' => $titleRecom,
                    'prenomDestin' => $firstNameDesti,
                    'nomDestin' => $lastNameDesti,
                ]);
            $mailer->send($email);

            // Envoi du mail de nouvelle recomandation pour JUST
            $email = (new TemplatedEmail())
                ->from('postmaster@openpixl.fr')
                ->to('contact@justafaire.fr')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('JUST à FAIRE - une nouvelle recommandation a été émise depuis le site')
                //->text('Sending emails is fun again!')
                ->htmlTemplate('email/newRecommandationWebMaster.html.twig')
                ->context([
                    'author' => $author,
                    'titleRecom' => $titleRecom,
                    'prenomDestin' => $firstNameDesti,
                    'nomDestin' => $lastNameDesti,
                ]);
            $mailer->send($email);

            return $this->redirectToRoute('op_gestapp_recommandation_index');
        }

        return $this->render('gest_app/recommandation/new.html.twig', [
            'recommandation' => $recommandation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/recommandation/{id}", name="op_gestapp_recommandation_show", methods={"GET"})
     */
    public function show(Recommandation $recommandation): Response
    {
        $isread = $recommandation->getIsRead();
        if($isread == 0) {
            $recommandation->setIsRead(0);
            $recommandation->setIsFirstView(1);
            $recommandation->setRecoState('InProgress');
            $this->getDoctrine()->getManager()->flush();

            return $this->render('gest_app/recommandation/show.html.twig', [
                'recommandation' => $recommandation,
            ]);
        }elseif($isread == 1){
            return $this->render('gest_app/recommandation/show2.html.twig', [
                'recommandation' => $recommandation,
            ]);
        }
        return $this->render('gest_app/recommandation/show.html.twig', [
            'recommandation' => $recommandation,
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/recommandation/{id}/edit", name="op_gestapp_recommandation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Recommandation $recommandation): Response
    {
        $isRead = $recommandation->getIsRead();
        $form = $this->createForm(RecommandationType::class, $recommandation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('op_gestapp_recommandation_index');
        }

        return $this->render('gest_app/recommandation/edit.html.twig', [
            'recommandation' => $recommandation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/recommandation/{id}", name="op_gestapp_recommandation_delete", methods={"POST"})
     */
    public function delete(Request $request, Recommandation $recommandation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recommandation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recommandation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_gestapp_recommandation_index');
    }

    /**
     * @Route("/op_admin/gestapp/recommandation/byuserReceipt/{hide}", name="op_gestapp_recommandation_byuserreceipt", methods={"GET"})
     */
    public function byUserReceipt($hide, RecommandationRepository $recommandationRepository): Response
    {
        $user = $this->getUser();
        $recommandations = $this->getDoctrine()->getRepository(Recommandation::class)->findByUserReceipt($user);
        return $this->render('gest_app/recommandation/byuserReceipt.html.twig', [
            'recommandations' => $recommandations,
            'hide' => $hide
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/recommandation/byuserSend/{hide}", name="op_gestapp_recommandation_byusersend", methods={"GET"})
     */
    public function byUserSend($hide,RecommandationRepository $recommandationRepository): Response
    {
        $user = $this->getUser();
        $recommandations = $this->getDoctrine()->getRepository(Recommandation::class)->findByUserSend($user);
        $hide = 1;
        return $this->render('gest_app/recommandation/byuserSend.html.twig', [
            'recommandations' => $recommandations,
            'hide' => $hide
        ]);
    }

    /**
     * @Route("/op_admin/gestapp/recommandation/compiluser", name="op_gestapp_recommandation_compiluser", methods={"GET"})
     */
    public function compilUser(): Response
    {
        $user = $this->getUser();
        $stats = $this->getDoctrine()->getRepository(Recommandation::class)->statsByUser($user);
        return $this->render('gest_app/recommandation/compiluser.html.twig', [
            'stats' => $stats,
        ]);
    }

    /**
     * Suppression d'une ligne index.php
     * @Route("/op_admin/gestapp/recommandation/del/{id}", name="op_gestapp_recommandation_suppr", methods={"POST"})
     */
    public function DelEvent(Request $request, Recommandation $recommandation) : Response
    {
        $author = $recommandation->getAuthor();
        $hide = 0;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($recommandation);
        $entityManager->flush();

        $receipt = $this->getDoctrine()->getRepository(Recommandation::class)->findByUserReceipt($author);

        return $this->json([
            'code'=> 200,
            'message' => "L'évènenemt a été supprimé",
            'listeReceipt' => $this->renderView('gest_app/recommandation/include/_liste.html.twig', [
                'recommandations' => $receipt,
                'hide' => $hide
            ]),
        ], 200);
    }

    /**
     * Mise a jour des recommandations depuis le show Recommandation
     * @Route("/op_admin/gestapp/recommandation/reco/{id}", name="op_gestapp_recommandation_addrecoprice",methods={"GET","POST"})
     */
    public function addrecoprice(Recommandation $recommandation, Request $request): Response
    {
        $user = $this->getUser();
        $hide = 0;
        $isread = $recommandation->getIsRead();

        if(!$user) {return $this->json([
            'code' => 403,
            'message'=> "Vous n'êtes pas connecté"
        ], 403);
        }elseif($isread == 0){
            $data = json_decode($request->getContent(), true);
            $recoprice = $data['recoprice'];

            // Hydratation de l'entité
            $recommandation->setIsRead('1');
            $recommandation->setRecoPrice($recoprice);
            $this->getDoctrine()->getManager()->flush();

            // Préparation de la liste à rafraichir
            $recommandations = $this->getDoctrine()->getRepository(Recommandation::class)->findByUserReceipt($user);

            // Réponse JSON
            return $this->json([
                'code'=> 200,
                'message' => "L'estimation de la recommandation a été ajoutée à la recommandation.",
                'listeReceipt' => $this->renderView('gest_app/recommandation/include/_liste.html.twig', [
                    'recommandations' => $recommandations,
                    'hide' => $hide
                ]),
            ], 200);
        }else{
            $data = json_decode($request->getContent(), true);
            $name = $data['name'];
            $description = $data['description'];
            $recoprice = $data['recoprice'];
            $recostate = $data['recostate'];
            $clientAddress = $data['clientAddress'];
            $clientComplement = $data['clientComplement'];
            $clientZipcode = $data['clientZipcode'];
            $clientCity = $data['clientCity'];

            // Hydratation de l'entité
            $recommandation->setRecoPrice($recoprice);
            $recommandation->setName($name);
            $recommandation->setRecoState($recostate);
            $recommandation->setDescription($description);
            $recommandation->setClientAddress($clientAddress);
            $recommandation->setClientCity($clientComplement);
            $recommandation->setClientZipcode($clientZipcode);
            $recommandation->setClientCity($clientCity);
            $this->getDoctrine()->getManager()->flush();

            // Préparation de la liste à rafraichir
            $recommandations = $this->getDoctrine()->getRepository(Recommandation::class)->findByUserReceipt($user);

            // Réponse JSON
            return $this->json([
                'code'=> 200,
                'message' => "L'estimation de la recommandation a été ajoutée à la recommandation.",
                'listeReceipt' => $this->renderView('gest_app/recommandation/include/_liste.html.twig', [
                    'recommandations' => $recommandations,
                    'hide' => $hide
                ]),
            ], 200);
        }
    }
}
