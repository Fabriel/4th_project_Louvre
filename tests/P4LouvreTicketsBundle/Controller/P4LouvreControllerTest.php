<?php

namespace P4Louvre\tests\P4LouvreTicketsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class P4LouvreControllerTest extends WebTestCase
{
    public function testIndexAndLinks()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Bienvenue', $client->getResponse()->getContent());

        $infosLink = $crawler->filter('p.lead a');
        $crawler = $client->click($infosLink->link());

        $this->assertContains('Le tarif "journée" est de :', $client->getResponse()->getContent());

        $bookingLink = $crawler->filter('p a');
        $crawler = $client->click($bookingLink->link());

        $this->assertEquals(1, $crawler->filter('h1:contains("Achat de billets")')->count());
    }

    public function testBookingForm()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/booking');

        $form = $crawler->selectButton('Poursuivre la commande')->form();
        $crawler = $client->submit($form, array(
            'p4louvre_ticketsbundle_booking[ticketDate]'        => '23/11/2017',
            'p4louvre_ticketsbundle_booking[email]'             => 'test@gmail.com',
            'p4louvre_ticketsbundle_booking[ticketType]'        => true,
            'p4louvre_ticketsbundle_booking[totalNbTickets]'    => '3'
        ));

        $this->assertEquals('P4Louvre\TicketsBundle\Controller\P4LouvreController::bookingAction', $client->getRequest()->attributes->get('_controller'));
    }
}
