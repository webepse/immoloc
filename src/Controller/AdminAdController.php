<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * Permet d'afficher l'ensemble des annonces 
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(AdRepository $repo, $page): Response
    {
        //@Route("/admin/ads/{page}", name="admin_ads_index", requirements={"page": "\d+"})
        // Méthode find: permet de retrouver un enregistrement par son identifiant
        // $ad = $repo->find(531);

        // $ad = $repo->findOneBy([
        //    'title' => "mon annonce"
        // ]);

        /* findBy permet de récupèrer plusieurs éléments (différent de findOneBy) - elle a 4 arguments possible 
            1- critère
            2- ordre
            3- limit
            4- offset (début)
            $ads = $repo->findBy([],[], 1, 0);
        */
        $limit = 10; // limite de 10 par page
        
        $start = $page * $limit - $limit;
        // page 1 * 10 = 10 - 10 = 0
        // page 2 * 10 = 20 - 10 = 10

        $total = count($repo->findAll());
        $pages = ceil($total / $limit ); // arrondi au dessus 3.4 => 4

        return $this->render('admin/ad/index.html.twig', [
            'ads' => $repo->findBy([],[], $limit, $start),
            'pages' => $pages,
            'page' => $page
        ]);
    }


    /**
     * Permet d'afficher le formulaire d'édition
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     * 
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                "success",
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée"
            );
        }

        return $this->render("admin/ad/edit.html.twig",[
            'ad' => $ad,
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager){
        // one ne peut pas supprimer une annonce qui possède des réservations
        if(count($ad->getBookings()) > 0){
            $this->addFlash(
                "warning",
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède déjà des réservations"
            );
        }else{
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
            );
            $manager->remove($ad);
            $manager->flush();

        }

        return $this->redirectToRoute('admin_ads_index');

    }
}
