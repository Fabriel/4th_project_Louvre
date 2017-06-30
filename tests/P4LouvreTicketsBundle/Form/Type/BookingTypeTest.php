<?php

namespace P4Louvre\tests\P4LouvreTicketsBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use P4Louvre\TicketsBundle\Form\Type\BookingType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class BookingTypeTest extends TypeTestCase
{
    private $entityManager;

    protected function setUp()
    {
        $this->entityManager = $this->createMock(ObjectManager::class);

        parent::setUp();
    }

    protected function getExtensions()
    {
        $type = new BookingType($this->entityManager);

        return array(
            new PreloadedExtension(array($type), array()),
        );
    }

    public function testSubmitValidData()
    {
        $formData = array(
            'ticketDate' => '23/11/2017',
            'email' => 'test@gmail.com',
            'ticketType' => true,
            'totalNbTickets' => 2
        );

        $form = $this->factory->create(BookingType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
