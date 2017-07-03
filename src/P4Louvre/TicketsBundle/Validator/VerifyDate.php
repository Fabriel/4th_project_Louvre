<?php

namespace P4Louvre\TicketsBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
* @Annotation
*/
class VerifyDate extends Constraint
{
    public $messageInvalidDate = "Cette date n'est pas valide.";

    public $messageSun = "L'achat de billets pour le dimanche se fait uniquement au guichet.";

    public $messageTue = "Le musée est fermé le mardi.";

    public $messageMay = "Le musée est fermé le 1er mai.";

    public $messageNovember = "Le musée est fermé le 1er novembre.";

    public $messageXmas = "Le musée est fermé le jour de Noël.";

    public $messageDay = "Il est plus de 14h00, vous ne pouvez plus acheter de billet journée pour aujourd'hui.";
}
