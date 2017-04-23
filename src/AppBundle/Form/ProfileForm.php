<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ProfileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	
        $builder
        ->add('email', TextType::class, array(
            'label' => 'E-Mail',
            'attr' => array('class' => 'form-control'),
        ))
        ->add('fullname', TextType::class, array(
            'label' => 'Full Name',
            'attr' => array('class' => 'form-control'),
        ))->add('address', TextareaType::class, array(
            'label' => 'Delivery Address',
            'attr' => array('class' => 'form-control'),
        ))->add('roles', ChoiceType::class, array(
            'choices' => array('USER' => 'ROLE_USER', 'ADMIN' => 'ROLE_ADMIN', 'EDITOR' => 'ROLE_EDITOR'),
            'multiple'  => true,
            'attr' => array('class' => 'multiple-controll'),
        ))->add('cash', TextType::class, array(
            'label' => 'Cash in wallet',
            'attr' => array('class' => 'form-control'),
        ))->add('password', PasswordType::class, array(
            'required'  => false,
            'attr' => array('class' => 'form-control'),
        ))->add('save', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-success', 'style' => 'margin-top: 24px;'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User'
        ]);
	}
}