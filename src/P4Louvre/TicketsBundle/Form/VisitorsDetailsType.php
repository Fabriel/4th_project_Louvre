<?php

namespace P4Louvre\TicketsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisitorsDetailsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visitorFirstName',   TextType::class)
            ->add('visitorName',        TextType::class)
            ->add('visitorDob',         DateType::Class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'invalid_message' => "Cette date n'est pas valide."
            ))
            ->add('visitorCountry',     CountryType::class, array(
                'preferred_choices' => array(
                    'BE', 'CH', 'DE', 'ES', 'FR', 'GB', 'IT', 'JP', 'NL', 'US'
                ),
                'data' => 'FR'
            ))
            ->add('reduceTicket',       CheckboxType::class, array(
                'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4Louvre\TicketsBundle\Entity\Visitors'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'p4louvre_ticketsbundle_visitors';
    }

}
