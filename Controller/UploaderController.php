<?php

namespace Stfalcon\Bundle\TinymceBundle\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class UploaderController extends Controller
{
    public function uploadAction(Request $request)
    {
        $fileOrig = $request->files->get('tiny_inner_image');
        $fileName = time() . '-' . $fileOrig->getClientOriginalName();
        $filePath = $this->container->getParameter('tinymce-savepath');
        if (!file_exists($filePath) && !is_dir($filePath)) {
            mkdir($filePath);
        }
        $fileOrig->move($filePath, $fileName);
        $response = new Response('<img src="/files/' . $fileName . '"/>');
        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }
}