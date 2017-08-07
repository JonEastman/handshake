<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HelloSignController extends Controller
{
    /**
     * @Route("/hello-sign/integrated", name="hellosign_integrated")
     */
    public function sendIntegratedRequestAction()
    {


        return $this->render('hellosign/send_request.html.twig');
    }

    /**
     * @Route("/hello-sign/website", name="hellosign_website")
     */
    public function sendWebsiteRequestAction()
    {


        return $this->render('hellosign/send_request.html.twig');
    }
}
