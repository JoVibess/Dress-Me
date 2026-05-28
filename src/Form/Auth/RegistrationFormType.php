<?php

namespace App\Form\Auth;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.email',
            ])
            ->add('website', UrlType::class, [
                'label' => 'form.website',
                'default_protocol' => 'https',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'form.password',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(message: 'validator.password_required'),
                    new Length(
                        min: 8,
                        minMessage: 'validator.password_too_short',
                        max: 4096,
                    ),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'form.agree_terms',
                'mapped' => false,
                'constraints' => [
                    new IsTrue(message: 'validator.agree_terms_required'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
