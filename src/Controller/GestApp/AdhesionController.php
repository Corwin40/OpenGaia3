<?php

namespace App\Controller\GestApp;

use App\Entity\Admin\Member;
use App\Entity\Admin\Structure;
use App\Entity\GestApp\Adhesion;
use App\Form\GestApp\AdhesionType;
use App\Repository\GestApp\AdhesionRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**

 */
class AdhesionController extends AbstractController
{
    /**
     * @Route("/gestapp/adhesion", name="op_gestapp_adhesion_index", methods={"GET"})
     */
    public function index(AdhesionRepository $adhesionRepository): Response
    {
        return $this->render('gest_app/adhesion/index.html.twig', [
            'adhesions' => $adhesionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/gestapp/adhesion/new", name="op_gestapp_adhesion_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $adhesion = new Adhesion();
        $form = $this->createForm(AdhesionType::class, $adhesion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adhesion);
            $entityManager->flush();

            return $this->redirectToRoute('gest_app_adhesion_index');
        }

        return $this->render('gest_app/adhesion/new.html.twig', [
            'adhesion' => $adhesion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gestapp/adhesion/new2", name="op_gestapp_adhesion_new2", methods={"GET","POST"})
     */
    public function new2(Request $request, MailerInterface $mailer): Response
    {
        $adhesion = new Adhesion();
        $form = $this->createForm(AdhesionType::class, $adhesion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adhesion);
            $entityManager->flush();

            // partie de code pour envoyer un email au membre recommandé
            $email = (new TemplatedEmail())
                ->from('postmaster@openpixl.fr')
                ->to($adhesion->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('JUST à FAIRE - une nouvelle recommandation vous attends dans votre espace')
                //->text('Sending emails is fun again!')
                ->htmlTemplate('email/newRecommandation.html.twig');
            $mailer->send($email);

            // partie de code pour envoyer un email à Just
            $email = (new TemplatedEmail())
                ->from('postmaster@openpixl.fr')
                ->to('contact@justafaire.fr')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('JUST à FAIRE - une nouvelle recommandation a été émise depuis le site')
                //->text('Sending emails is fun again!')
                ->htmlTemplate('email/newAdhesionWebMaster.html.twig')
                ->context([
                    'id' => $adhesion->getId(),
                    'nameSociety' =>$adhesion->getFirstName(),
                    'firstName'=>$adhesion->getLastName(),
                    'lastName' => $adhesion->getPhoneGsm(),
                    'phonedesk' => $adhesion->getPhoneDesk(),
                    'Email' => $adhesion->getEmail()
                ]);
            $mailer->send($email);

            return $this->redirectToRoute('op_webapp_public_index');
        }

        return $this->render('gest_app/adhesion/new2.html.twig', [
            'adhesion' => $adhesion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gestapp/adhesion/add", name="op_gestapp_adhesion_add", methods={"GET","POST"})
     */
    public function addAdhesion(Request $request): Response
    {
        $adhesion = new Adhesion();
        $form = $this->createForm(AdhesionType::class, $adhesion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adhesion);
            $entityManager->flush();

            return $this->redirectToRoute('op_webapp_public_index');
        }

        return $this->render('gest_app/adhesion/add.html.twig', [
            'adhesion' => $adhesion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gestapp/adhesion/{id}", name="op_gestapp_adhesion_show", methods={"GET"})
     */
    public function show(Adhesion $adhesion): Response
    {
        return $this->render('gest_app/adhesion/show.html.twig', [
            'adhesion' => $adhesion,
        ]);
    }

    /**
     * @Route("/gestapp/adhesion/{id}/edit", name="op_gestapp_adhesion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Adhesion $adhesion): Response
    {
        $form = $this->createForm(AdhesionType::class, $adhesion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gest_app_adhesion_index');
        }

        return $this->render('gest_app/adhesion/edit.html.twig', [
            'adhesion' => $adhesion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gestapp/adhesion/{id}", name="op_gestapp_adhesion_delete", methods={"POST"})
     */
    public function delete(Request $request, Adhesion $adhesion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adhesion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($adhesion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gest_app_adhesion_index');
    }

    /**
     * @Route("/gestapp/adhesion/update/id", name="op_gestapp_adhesion_update", methods={"GET","POST"})
     */
    public function update(Adhesion $adhesion)
    {

    }

    /**
     * @Route("/gestapp/adhesion/addMember/{id}", name="op_gestapp_adhesion_addMember", methods={"GET","POST"})
     */
    public function addMember(Adhesion $adhesion, UserPasswordEncoderInterface $passwordEncoder)
    {
        $structure = new Structure();
        $structure->setName($adhesion->getSociety());
        $structure->setAddress($adhesion->getAddress());
        $structure->setZipcode($adhesion->getZipcode());
        $structure->setCity($adhesion->getCity());
        $structure->setRespFirstName($adhesion->getGerantFirstname());
        $structure->setRespLastName($adhesion->getGerantLastname());
        $structure->setSiret($adhesion->getSiret());
        $structure->setEmailStruct($adhesion->getEmail());
        $structure->setPhoneDesk($adhesion->getPhoneDesk());
        $structure->setPhoneGsm($adhesion->getPhoneGsm());
        $structure->setUrlWeb($adhesion->getUrlWeb());
        $structure->setUrlFacebook($adhesion->getUrlFacebook());
        $structure->setUrlInstagram($adhesion->getUrlInstagram());
        $structure->setUrlLinkedin($adhesion->getUrlLinkedin());
        $structure->setFirstActivity($adhesion->getFirstActivity());
        $structure->setSecondActivity($adhesion->getSecondActivity());
        $structure->setProjectDev($adhesion->getProjectDev());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($structure);
        $entityManager->flush();

        $member = new Member;
        $member->setEmail($adhesion->getEmail());
        $member->setPassword($passwordEncoder->encodePassword(
            $member,
            'JustaFaire')
        );

        $member->setFirstName($adhesion->getFirstName());
        $member->setLastName($adhesion->getLastName());
        $member->setStructure($structure);
        $entityManager->persist($member);
        $entityManager->flush();

        return $this->json([
            'code'          => 200,
            'message'       => 'La page est publiée'
        ], 200);
    }
}
