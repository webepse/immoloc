<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_bookings_index")
     */
    public function index(BookingRepository $repo): Response
    {
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $repo->findAll()
        ]);
    }

    /**
     * Permet d'éditer une reservation
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     *
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking, [
            'validation_groups' => ['Default']
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $booking->setAmount(0); // 0 = empty -> pour activer la fonction PreUpdate de l'entité Booking
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n°<strong>{$booking->getId()}</strong> a bien été modifiée"
            );
        
        }

        return $this->render('admin/booking/edit.html.twig',[
            'booking' => $booking,
            'myForm' => $form->createView()
        ]);
    }


    /**
     * Permet de supprimer une réservation
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     *
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager)
    {
        $this->addFlash(
            'success',
            "La réservation n°<strong>{$booking->getId()}</strong> a bien été supprimée"
        );

        $manager->remove($booking);
        $manager->flush();

        return $this->redirectToRoute("admin_bookings_index");
    }


}
