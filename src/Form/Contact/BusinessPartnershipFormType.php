<?php

namespace App\Form\Contact;

use App\Dto\BusinessPartnershipRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BusinessPartnershipFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contactType', HiddenType::class, [
                'data' => 'business_partnership',
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Work email',
            ])
            ->add('company', TextType::class, [
                'label' => 'Company',
            ])
            ->add('website', UrlType::class, [
                'label' => 'Website',
                'required' => false,
                'default_protocol' => 'https',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Partnership goal',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BusinessPartnershipRequest::class,
        ]);
    }
}
