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
        if($this->configManager->hasConfig('config')) {
            $config = $this->configManager->getConfig('config');
        } else {
            $config = [];
        }

        $checkTypes = [
            'config_name', 'language', 'selector', 'plugins',
            'toolbar', 'quickbars_selection_toolbar', 'quickbars_insert_toolbar',
            'valid_elements', 'file_picker',
        ];

        foreach ($checkTypes as $type) {
            if(isset($options[$type]) && $options[$type] !== null) {
                $config[$type] = $options[$type];
            }
        }

        $this->configManager->setConfig('config', $config);
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
                'language' => null,
                'selector' => null,
                'plugins' => null,
                'toolbar' => null,
                'quickbars_selection_toolbar' => null,
                'quickbars_insert_toolbar' => null,
                'valid_elements' => null,
                'file_picker' => null,
                'enable' => $this->enable,
            ])
        ->setAllowedTypes('config_name', ['string', 'null'])
        ->setAllowedTypes('language', ['string', 'null'])
        ->setAllowedTypes('selector', ['string', 'null'])
        ->setAllowedTypes('plugins', ['string', 'null'])
        ->setAllowedTypes('toolbar', ['string', 'null'])
        ->setAllowedTypes('quickbars_selection_toolbar', ['string', 'null'])
        ->setAllowedTypes('quickbars_insert_toolbar', ['string', 'null'])
        ->setAllowedTypes('valid_elements', ['string', 'null'])
        ->setAllowedTypes('file_picker', ['array', 'null'])
        ->setAllowedTypes('enable', 'bool')
        ;
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
