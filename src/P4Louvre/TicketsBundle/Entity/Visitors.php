<?php

namespace P4Louvre\TicketsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use P4Louvre\TicketsBundle\Entity\Booking;

/**
 * Visitors
 *
 * @ORM\Table(name="visitors")
 * @ORM\Entity(repositoryClass="P4Louvre\TicketsBundle\Repository\VisitorsRepository")
 */
class Visitors
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="P4Louvre\TicketsBundle\Entity\Booking", inversedBy="visitors", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $booking;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ticketDate", type="date")
     */
    private $ticketDate;

    /**
     * @var string
     *
     * @ORM\Column(name="visitorName", type="string", length=255)
     */
    private $visitorName;

    /**
     * @var string
     *
     * @ORM\Column(name="visitorFirstName", type="string", length=255)
     */
    private $visitorFirstName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="visitorDob", type="date")
     *
     * @Assert\Date(message="Cette date n'est pas valide")
     */
    private $visitorDob;

    /**
     * @var string
     *
     * @ORM\Column(name="visitorCountry", type="string", length=2)
     *
     * @Assert\Country()
     */
    private $visitorCountry;

    /**
     * @var bool
     *
     * @ORM\Column(name="reduceTicket", type="boolean")
     */
    private $reduceTicket;

    /**
     * @var int
     *
     * @ORM\Column(name="ticketPrice", type="integer")
     */
    private $ticketPrice;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set booking
     *
     * @param \P4Louvre\TicketsBundle\Entity\Booking $booking
     *
     * @return Visitors
     */
    public function setBooking(Booking $booking)
    {
        $this->booking = $booking;

        return $this;
    }

    /**
     * Get booking
     *
     * @return \P4Louvre\TicketsBundle\Entity\Booking
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Visitors
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ticketDate
     *
     * @param \DateTime $ticketDate
     *
     * @return Visitors
     */
    public function setTicketDate($ticketDate)
    {
        $this->ticketDate = $ticketDate;

        return $this;
    }

    /**
     * Get ticketDate
     *
     * @return \DateTime
     */
    public function getTicketDate()
    {
        return $this->ticketDate;
    }

    /**
     * Set visitorName
     *
     * @param string $visitorName
     *
     * @return Visitors
     */
    public function setVisitorName($visitorName)
    {
        $this->visitorName = strtoupper($visitorName);

        return $this;
    }

    /**
     * Get visitorName
     *
     * @return string
     */
    public function getVisitorName()
    {
        return $this->visitorName;
    }

    /**
     * Set visitorFirstName
     *
     * @param string $visitorFirstName
     *
     * @return Visitors
     */
    public function setVisitorFirstName($visitorFirstName)
    {
        $this->visitorFirstName = ucfirst($visitorFirstName);

        return $this;
    }

    /**
     * Get visitorFirstName
     *
     * @return string
     */
    public function getVisitorFirstName()
    {
        return $this->visitorFirstName;
    }

    /**
     * Set visitorDob
     *
     * @param \DateTime $visitorDob
     *
     * @return Visitors
     */
    public function setVisitorDob($visitorDob)
    {
        $this->visitorDob = $visitorDob;

        return $this;
    }

    /**
     * Get visitorDob
     *
     * @return \DateTime
     */
    public function getVisitorDob()
    {
        return $this->visitorDob;
    }

    /**
     * Set visitorCountry
     *
     * @param string $visitorCountry
     *
     * @return Visitors
     */
    public function setVisitorCountry($visitorCountry)
    {
        $this->visitorCountry = $visitorCountry;

        return $this;
    }

    /**
     * Get visitorCountry
     *
     * @return string
     */
    public function getVisitorCountry()
    {
        return $this->visitorCountry;
    }

    /**
     * Set reduceTicket
     *
     * @param boolean $reduceTicket
     *
     * @return Visitors
     */
    public function setReduceTicket($reduceTicket)
    {
        $this->reduceTicket = $reduceTicket;

        return $this;
    }

    /**
     * Get reduceTicket
     *
     * @return bool
     */
    public function getReduceTicket()
    {
        return $this->reduceTicket;
    }

    /**
     * Set ticketPrice
     *
     * @param integer $ticketPrice
     *
     * @return Visitors
     */
    public function setTicketPrice($ticketPrice)
    {
        $this->ticketPrice = $ticketPrice;

        return $this;
    }

    /**
     * Get ticketPrice
     *
     * @return integer
     */
    public function getTicketPrice()
    {
        return $this->ticketPrice;
    }
}
