<?php

namespace P4Louvre\TicketsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('P4LouvreTicketsBundle:Default:index.html.twig');
    }
}
