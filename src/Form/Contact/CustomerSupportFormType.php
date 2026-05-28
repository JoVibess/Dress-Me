<?php

namespace App\Form\Contact;

use App\Dto\CustomerSupportRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerSupportFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contactType', HiddenType::class, [
                'data' => 'customer_support',
            ])
            ->add('name', TextType::class, [
                'label' => 'form.name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.email',
            ])
            ->add('subject', TextType::class, [
                'label' => 'form.subject',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.message',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerSupportRequest::class,
        ]);
    }
}
