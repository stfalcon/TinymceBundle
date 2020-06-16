<?php

namespace Stfalcon\Bundle\TinymceBundle\Form\Type;

use Stfalcon\Bundle\TinymceBundle\Model\ConfigManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TinyMCEType
 * @author Łukasz Wątor
 */
class TinyMCEType extends AbstractType
{
    /** @var ConfigManagerInterface */
    private $configManager;

    /** @var bool */
    private $enable = true;

    /** @var int */
    private static $id = 1;

    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @param bool|null $isEnable
     * @return bool
     */
    public function isEnable($isEnable = null)
    {
        if(is_bool($isEnable)) {
            $this->enable = $isEnable;
        }

        return $this->enable;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $config = $form->getConfig();
        $view->vars['enable'] = $config->getAttribute('enable');

        if (!$view->vars['enable']) {
            return;
        }

        if(!isset($view->vars['attr'])) {
            $view->vars['attr'] = [];
        }
        if(!isset($view->vars['attr']['class'])) {
            $view->vars['attr']['class'] = '';
        }

        $view->vars['attr']['class'] .= ' tinymce';
        $view->vars['attr']['data-tinymce'] = '' . (self::$id ++);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('enable', $options['enable']);

        if (!$options['enable']) {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'config_name' => $this->configManager->getDefaultConfig(),
                'enable' => $this->enable,
            ])
        ->setAllowedTypes('enable', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return method_exists(AbstractType::class, 'getBlockPrefix') ? TextareaType::class : 'textarea';
    }

    /**
     * @return string
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
