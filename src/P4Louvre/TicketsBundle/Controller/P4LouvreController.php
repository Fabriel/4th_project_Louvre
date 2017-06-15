<?php

namespace P4Louvre\TicketsBundle\Controller;

use P4Louvre\TicketsBundle\Entity\Booking;
use P4Louvre\TicketsBundle\Entity\Visitors;
use P4Louvre\TicketsBundle\Form\BookingType;
use P4Louvre\TicketsBundle\Form\VisitorsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class P4LouvreController extends Controller
{
    /**
     * @Route("/", name="p4_louvre_homepage")
     */
    public function indexAction()
    {
        return $this->render('P4LouvreTicketsBundle:Booking:index.html.twig');
    }

    /**
     * @Route("/infos", name="p4_louvre_infos")
     */
    public function infosAction()
    {
        return $this->render('P4LouvreTicketsBundle:Booking:infos.html.twig');
    }

    /**
     * @Route("/booking", name="p4_louvre_booking")
     */
    public function bookingAction(Request $request)
    {
        $booking = new Booking();
        $form   = $this->get('form.factory')->create(BookingType::class, $booking);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $booking->setTotalPrice(0);
            $booking->setCommandReference('');
            $em->persist($booking);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Veuillez renseigner le formulaire ci-dessous pour finaliser votre commande.');

            return $this->redirectToRoute('p4_louvre_booking_visitors/id', array('id' => $booking->getId()));
        }

        return $this->render('P4LouvreTicketsBundle:Booking:booking.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/booking/visitors/{id}", name="p4_louvre_booking_visitors/id")
     */
    public function visitorsAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $em->getRepository('P4LouvreTicketsBundle:Booking')->find($id);
        $nbVisitors = $booking->getTotalNbTickets();
        $ticketDate = $booking->getTicketDate();

        for($i = 1 ; $i <= $nbVisitors; $i++) {
            ${'visitor'.$i.'_b'.$id} = new Visitors();
            ${'visitor'.$i.'_b'.$id}->setName('visitor'.$i.'_b'.$id);
            ${'visitor'.$i.'_b'.$id}->setBooking($booking);
            ${'visitor'.$i.'_b'.$id}->setTicketDate($ticketDate);
            $booking->addVisitor(${'visitor'.$i.'_b'.$id});
        }

        $form = $this->get('form.factory')->create(VisitorsType::class, $booking);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $ref = $this->randomStr(10);
            $ref = substr( ${'visitor1_b'.$id}->getVisitorName(), 0, 3) . '-' . $ref;
            $booking->setCommandReference($ref);
            $em->persist($booking);
            $visitors = $booking->getVisitors();

            foreach($visitors as $visitor) {
                $em->persist($visitor);
            }

            $em->flush();

            $priceCalculation = $this->get('p4_louvre_tickets.ticketprice');
            $totalPrice = $priceCalculation->totalPriceCalculation($booking);
            $booking->setTotalPrice($totalPrice);

            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Veuillez vérifier votre commande et procéder au règlement.');

            return $this->redirectToRoute('p4_louvre_booking_summary/id', array('id' => $booking->getId()));
        }

        return $this->render('P4LouvreTicketsBundle:Booking:visitors.html.twig', array(
            'form' => $form->createView(),
            'nbVisitors' => $nbVisitors,
            'bookingId' => $id));
    }

    /**
     * @Route("/booking/summary/{id}", name="p4_louvre_booking_summary/id")
     */
    public function summaryAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $em->getRepository('P4LouvreTicketsBundle:Booking')->find($id);

        return $this->render('P4LouvreTicketsBundle:Booking:summary.html.twig', array(
            'booking' => $booking,
            'bookingId' => $id));
    }

    /**
     * @Route("/booking/cancel/{id}", name="p4_louvre_booking_cancel")
     */
    public function cancelAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $em->getRepository('P4LouvreTicketsBundle:Booking')->find($id);

        $em->remove($booking);
        $em->flush();

        $request->getSession()->getFlashBag()->add('info', 'Votre commande a bien été annulée.');

        return $this->redirectToRoute('p4_louvre_homepage');
    }

    /**
     * Generate the command's reference
     *
     * @param $number
     *
     * @return string
     */
    public function randomStr($number) {
        $ref = date('Ymd') . '-';
        $string = 'A0B1C2D3E4F5G6H7I8J9K0L1M2N3O4P5Q6R7S8U9T0V4W5X6Y7Z5a6b7c8d9e0f1g2h3i4j5k6l7m8n9o0p1q2r3s4t5u6v7w8x9y0z1';
        $nbChars = strlen($string);

        for($i = 0; $i < $number; $i++)
        {
            $ref .= $string[rand(0, ($nbChars-1))];
        }

        return $ref;
    }

}
