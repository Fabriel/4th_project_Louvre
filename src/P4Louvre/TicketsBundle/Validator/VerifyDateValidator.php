<?php

namespace P4Louvre\TicketsBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VerifyDateValidator extends ConstraintValidator
{
    public function validate($date, Constraint $constraint)
    {
        if(!$date instanceof \DateTime) {
            $this->context->addViolation($constraint->messageInvalidDate);
        }

        $day = $date->format('D');
        $dateAndMonth = $date->format('d/m');
        $completeDate = $date->format('d/m/Y');
        $today = date('d/m/Y');
        $now = date('H:i:s');

        if($day == 'Sun') {
            $this->context->addViolation($constraint->messageSun);
        } elseif($day == 'Tue') {
            $this->context->addViolation($constraint->messageTue);
        } elseif($dateAndMonth == '01/05') {
            $this->context->addViolation($constraint->messageMay);
        } elseif($dateAndMonth == '01/11') {
            $this->context->addViolation($constraint->messageNovember);
        } elseif($dateAndMonth == '25/12') {
            $this->context->addViolation($constraint->messageXmas);
        } elseif($today == $completeDate && strcmp($now, '12:00:00') > 0) {
            $this->context->addViolation($constraint->messageDay);
        }
    }
}
