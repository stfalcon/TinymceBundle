<?php

namespace Stfalcon\Bundle\TinymceBundle\Form\Type;

use Stfalcon\Bundle\TinymceBundle\Model\ConfigManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TinyMCEType
 * @author Łukasz Wątor
 */
class TinyMCEType extends AbstractType
{
    /** @var ConfigManagerInterface */
    private $configManager;

    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'config_name' => $this->configManager->getDefaultConfig(),
            ]);
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
