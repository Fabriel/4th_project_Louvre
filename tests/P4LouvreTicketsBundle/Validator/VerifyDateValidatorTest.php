<?php

namespace P4Louvre\tests\P4LouvreTicketsBundle\Validator;

use DateTime;
use P4Louvre\TicketsBundle\Validator\VerifyDate;
use P4Louvre\TicketsBundle\Validator\VerifyDateValidator;
use PHPUnit\Framework\TestCase;

class VerifyDateValidatorTest extends TestCase
{
    private $constraint;
    private $context;

    public function setUp()
    {
        $this->constraint = new VerifyDate();
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContext')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testDay()
    {
        $day = new DateTime('2017-11-28');
        $validator = new VerifyDateValidator();
        $validator->initialize( $this->context);

        $this->context->expects($this->once())
            ->method('addViolation')
            ->with($this->constraint->messageTue);
        $validator->validate($day, $this->constraint);
    }

    public function testDateAndMonth()
    {
        $date = new DateTime('2017-12-25');
        $validator = new VerifyDateValidator();
        $validator->initialize( $this->context);

        $this->context->expects($this->once())
            ->method('addViolation')
            ->with($this->constraint->messageXmas);
        $validator->validate($date, $this->constraint);
    }
}
