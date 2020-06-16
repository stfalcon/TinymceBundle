<?php

namespace Stfalcon\Bundle\TinymceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TinyMCEType
 * @author Łukasz Wątor
 */
class TinyMCEType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return method_exists(AbstractType::class, 'getBlockPrefix') ? TextareaType::class : 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tinymce';
    }
}
