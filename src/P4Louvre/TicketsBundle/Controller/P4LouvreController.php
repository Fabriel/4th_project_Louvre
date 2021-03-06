<?php

namespace P4Louvre\TicketsBundle\Controller;

use DateTime;
use P4Louvre\TicketsBundle\Entity\Booking;
use P4Louvre\TicketsBundle\Entity\Visitors;
use P4Louvre\TicketsBundle\Form\Type\BookingType;
use P4Louvre\TicketsBundle\Form\Type\VisitorsType;
use Stripe\Charge;
use Stripe\Error\Card;
use Stripe\Stripe;
use Swift_Image;
use Swift_Message;
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
        $purger = $this->get('p4_louvre_tickets.purger');
        $hour = 1;
        $purger->purge($hour);

        $booking = new Booking();
        $form   = $this->get('form.factory')->create(BookingType::class, $booking);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post = $request->request->get('p4louvre_ticketsbundle_booking');
            $date = DateTime::createFromFormat('d/m/Y', $post['ticketDate'])->format('Y-m-d');
            $nbTicketsSold = $em->getRepository('P4LouvreTicketsBundle:Visitors')->findNbTicketsByDate($date);

            if((count($nbTicketsSold) + intval($post['totalNbTickets'])) > 1000)
            {
                $rest = 1000 - count($nbTicketsSold);
                if($rest > 0)
                {
                    $request->getSession()->getFlashBag()->add('info', 'Il ne reste que ' . $rest . ' billet(s) pour cette date.');
                    return $this->redirect($_SERVER['HTTP_REFERER']);;
                }
                $request->getSession()->getFlashBag()->add('info', 'Tous les billets ont été vendus pour cette date, veuillez en choisir une autre.');
                return $this->redirect($_SERVER['HTTP_REFERER']);;
            }

            $booking->setTotalPrice(0);
            $ref = $this->randomStrAction(5);
            $booking->setCommandReference($ref);
            $em->persist($booking);
            $em->flush();
            $request->getSession()->set('step1', $booking);

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
        $step1 = $request->getSession()->get('step1');
        if(!empty($step1)) {
            $em = $this->getDoctrine()->getManager();
            $booking = $em->getRepository('P4LouvreTicketsBundle:Booking')->find($id);
            $nbVisitors = $booking->getTotalNbTickets();
            $ticketDate = $booking->getTicketDate();

            for ($i = 1; $i <= $nbVisitors; $i++) {
                ${'visitor' . $i . '_b' . $id} = new Visitors();
                ${'visitor' . $i . '_b' . $id}->setName('visitor' . $i . '_b' . $id);
                ${'visitor' . $i . '_b' . $id}->setBooking($booking);
                ${'visitor' . $i . '_b' . $id}->setTicketDate($ticketDate);
                ${'visitor' . $i . '_b' . $id}->setTicketPrice(0);
                $booking->addVisitor(${'visitor' . $i . '_b' . $id});
            }

            $form = $this->get('form.factory')->create(VisitorsType::class, $booking);

            if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
                $ref = $this->randomStrAction(10);
                $ref = substr(${'visitor1_b' . $id}->getVisitorName(), 0, 3) . '-' . $ref;
                $booking->setCommandReference($ref);
                $em->persist($booking);
                $visitors = $booking->getVisitors();

                $priceCalculation = $this->get('p4_louvre_tickets.ticketprice');

                foreach ($visitors as $visitor) {
                    $em->persist($visitor);
                    $em->flush();
                    $ticketPrice = $priceCalculation->ticketPriceCalculation($visitor);
                    $visitor->setTicketPrice($ticketPrice);
                    $em->persist($visitor);
                }

                $totalPrice = $priceCalculation->totalPriceCalculation($booking);
                $booking->setTotalPrice($totalPrice);

                $em->flush();

                $request->getSession()->set('step2', $booking);

                $request->getSession()->getFlashBag()->add('info', 'Veuillez vérifier votre commande avant de procéder au règlement.');

                return $this->redirectToRoute('p4_louvre_booking_summary/id', array('id' => $booking->getId()));
            }

            return $this->render('P4LouvreTicketsBundle:Booking:visitors.html.twig', array(
                'form' => $form->createView(),
                'nbVisitors' => $nbVisitors,
                'bookingId' => $id
            ));
        }

        return $this->render('P4LouvreTicketsBundle:Booking:index.html.twig');
    }

    /**
     * @Route("/booking/summary/{id}", name="p4_louvre_booking_summary/id")
     */
    public function summaryAction($id, Request $request)
    {
        $step2 = $request->getSession()->get('step2');
        if(!empty($step2)) {
            $em = $this->getDoctrine()->getManager();
            $booking = $em->getRepository('P4LouvreTicketsBundle:Booking')->find($id);

            $request->getSession()->set('step3', $booking);

            return $this->render('P4LouvreTicketsBundle:Booking:summary.html.twig', array(
                'booking' => $booking,
                'bookingId' => $id
            ));
        }

        return $this->render('P4LouvreTicketsBundle:Booking:index.html.twig');
    }

    /**
     * @Route("/booking/pre-checkout/{id}", name="p4_louvre_booking_pre_checkout")
     */
    public function preCheckoutAction($id, Request $request)
    {

        $step3 = $request->getSession()->get('step3');
        if(!empty($step3)) {
            $em = $this->getDoctrine()->getManager();
            $booking = $em->getRepository('P4LouvreTicketsBundle:Booking')->find($id);
            $date = $booking->getTicketDate()->format('Y-m-d');
            $nbTicketsSold = $em->getRepository('P4LouvreTicketsBundle:Visitors')->findNbTicketsByDate($date);

            if((count($nbTicketsSold) + intval($booking->getTotalNbTickets())) > 1000)
            {
                $rest = 1000 - count($nbTicketsSold);
                if($rest > 0)
                {
                    $request->getSession()->getFlashBag()->add('info', 'Depuis le début de votre commande, d\'autres ont été effectuées, 
                    et il ne reste plus que ' . $rest . ' billet(s) pour cette date.');
                    $request->getSession()->clear();
                    return $this->redirectToRoute('p4_louvre_homepage');
                }
                $request->getSession()->getFlashBag()->add('info', 'Depuis le début de votre commande, d\'autres ont été finalisées, 
                et tous les billets ont été vendus pour cette date ; veuillez en choisir une autre.');
                return $this->redirectToRoute('p4_louvre_homepage');
            }

            $request->getSession()->set('step4', $booking);

            return $this->render('P4LouvreTicketsBundle:Booking:preCheckout.html.twig', array(
                'booking' => $booking,
                'bookingId' => $id
            ));
        }

        return $this->render('P4LouvreTicketsBundle:Booking:index.html.twig');
    }

    /**
     * @Route("/booking/checkout/{id}", name="p4_louvre_booking_checkout")
     */
    public function checkoutAction($id, Request $request)
    {
        $step4 = $request->getSession()->get('step4');
        if(!empty($step4)) {
            $em = $this->getDoctrine()->getManager();
            $booking = $em->getRepository('P4LouvreTicketsBundle:Booking')->find($id);
            $price = $booking->getTotalPrice();
            Stripe::setApiKey('sk_test_ngJqB6Hs03miplo3Xfjuz4xV');
            $token = $request->request->get('stripeToken');

            try {
                $charge = Charge::create(array(
                    'amount' => $price * 100,
                    'currency' => 'eur',
                    'source' => $token,
                    'description' => 'Paiement Stripe - Réservation Louvre'
                ));
                $message = new Swift_Message();
                $imgUrl = $message->embed(Swift_Image::fromPath('C:/wamp64/www/P4Louvre/web/img/logo_louvre.jpg'));
                $message->setSubject('Vos billets pour le Musée du Louvre')
                    ->setFrom(array('fabrice.loubier@gmail.com' => 'Billetterie du Musée du Louvre'))
                    ->setTo($booking->getEmail())
                    ->setContentType('text/html')
                    ->setCharset('utf-8')
                    ->setBody(
                        $this->renderView(
                            '@P4LouvreTickets/Emails/registration.html.twig',
                            array(
                                'booking'   => $booking,
                                'visitors'  => $booking->getVisitors(),
                                'url'      => $imgUrl,
                            )
                        ));
                $this->get('mailer')->send($message);
                $this->addFlash('info', 'Votre paiement a été accepté ; un email vient de vous être envoyé.');
                $booking->setPaid(true);
                $em->flush();
                $request->getSession()->clear();
                return $this->redirectToRoute('p4_louvre_homepage');
            } catch (Card $e) {
                $this->addFlash('info', 'Votre paiement a été rejeté, merci de réessayer.');
                return $this->redirect($_SERVER['HTTP_REFERER']);;
            }
        }

        return $this->render('P4LouvreTicketsBundle:Booking:index.html.twig');

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

        $request->getSession()->clear();

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
    public function randomStrAction($number) {
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
