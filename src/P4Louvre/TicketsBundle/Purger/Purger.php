<?php

namespace P4Louvre\TicketsBundle\Purger;

use DateTime;
use Doctrine\ORM\EntityManager;

class Purger
{
    private $em = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function purge($hour)
    {
        $bookingRepository = $this->em->getRepository('P4LouvreTicketsBundle:Booking');

        $date = new DateTime();
        $date->modify('-' . $hour . 'hour');

        $listBookings = $bookingRepository->getBookingsUnpaidBefore($date);

        foreach ($listBookings as $booking) {
            $this->em->remove($booking);
        }

        $this->em->flush();
    }
}
