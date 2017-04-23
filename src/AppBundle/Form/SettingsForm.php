<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SettingsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	
        $builder
        ->add('shopname', TextType::class, array(
            'attr' => array('class' => 'form-control'),
        ))
        ->add('address', TextareaType::class, array(
            'attr' => array('class' => 'form-control'),
        ))->add('globalpromo', TextType::class, array(
            'label' => 'Global promo % for all products',
            'attr' => array('class' => 'form-control'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Setting'
        ]);
	}
}