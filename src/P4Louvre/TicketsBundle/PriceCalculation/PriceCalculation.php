<?php

namespace P4Louvre\TicketsBundle\PriceCalculation;

use Doctrine\ORM\EntityManager;

class PriceCalculation
{
    private $em = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function ticketPriceCalculation($id)
    {
        $visitor = $this->em->getRepository('P4LouvreTicketsBundle:Visitors')->find($id);
        $dob = $visitor->getVisitorDob();
        $reduceTicket = $visitor->getReduceTicket();
        
        $booking = $visitor->getBooking();
        $date = $booking->getTicketDate();
        $ticketType = $booking->getTicketType();

        $age = ($dob->diff($date))->y;

        if ($reduceTicket == 1) {
            $ticketPrice = 10;
        } else {
            if ($age < 4) {
                $ticketPrice = 0;
            } elseif ($age > 4 && $age < 12) {
                $ticketPrice = 8;
            } elseif ($age >= 12 && $age < 60) {
                $ticketPrice = 16;
            } elseif ($age >= 60) {
                $ticketPrice = 12;
            }

            if ($ticketType == 0) {
                $ticketPrice = $ticketPrice / 2;
            }
        }

        return $ticketPrice;
    }

    public function totalPriceCalculation($id)
    {
        $booking = $this->em->getRepository('P4LouvreTicketsBundle:Booking')->find($id);

        $visitors = $booking->getVisitors();

        $totalPrice = $booking->getTotalPrice();

        foreach ($visitors as $visitor) {
            $totalPrice += $this->ticketPriceCalculation($visitor->getId());
        }

        return $totalPrice;
    }
}
