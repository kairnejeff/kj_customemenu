<?php

namespace PrestaShop\Module\ProductCarrousel\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductCarrouselType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'file (image or video)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/*',
                            'video/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image or video document',
                    ])
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Name',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PrestaShop\Module\ProductCarrousel\Entity\ProductCarrousel'
        ));
    }


}