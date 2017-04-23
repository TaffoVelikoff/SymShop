<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	
        $builder
        ->add('name', TextType::class, array(
            'label' => 'Category Name',
            'attr' => array('class' => 'form-control'),
        ))->add('promo', TextType::class, array(
            'label' => 'Promo %',
            'required' => false,
            'attr' => array('class' => 'form-control'),
        ))->add('save', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-success', 'style' => 'margin-top: 24px;'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Category'
        ]);
	}
}