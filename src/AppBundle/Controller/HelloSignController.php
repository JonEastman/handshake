<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HelloSignController extends Controller
{
    /**
     * @Route("/hello-sign", name="hello_sign_test")
     */
    public function indexAction()
    {


        return $this->render('hellosign/send_request.html.twig');
    }
}
