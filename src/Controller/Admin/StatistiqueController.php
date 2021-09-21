<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Member;
use App\Entity\GestApp\Recommandation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiqueController extends AbstractController
{
    /**
     * @Route("/admin/statistique", name="op_admin_statistique_index")
     */
    public function index(): Response
    {
        return $this->render('admin/statistique/index.html.twig', [
            'controller_name' => 'StatistiqueController',
        ]);
    }

    /**
     * Methode pour les CountUp
     * @Route("/admin/statistique/CountUp", name="op_admin_statistique_CountUp")
     */
    public function CountUp()
    {
        // requete pour calculer le chiffre d'affiare du Just
        $countRecomms = $this->getDoctrine()->getRepository(Recommandation::class)->CountCA();
        $CountMembers = $this->getDoctrine()->getRepository(Member::class)->findBy(array('type'=>'member'));
        // requete pour calculer le nombre de recommandation du Just
        //$countRecom = $this->getDoctrine()->getRepository(Recommandation::class)->CountRecom();

        return $this->render('admin/admin/countup.html.twig', [
            'countRecomms' => $countRecomms,
            'CountMembers' => $CountMembers
        ]);
    }
}
