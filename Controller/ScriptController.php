<?php

namespace Stfalcon\Bundle\TinymceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

/**
 * @author Stepan Tanasiychuk <stepan.tanasiychuk@gmail.com>
 */
class ScriptController extends BaseController
{

    public function initAction()
    {
        return $this->render('StfalconTinymceBundle:Script:init.html.twig', array());
    }
}