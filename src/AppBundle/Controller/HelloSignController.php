<?php

namespace AppBundle\Controller;

use AppBundle\Form\HandshakeType;
use AppBundle\Model\Handshake;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
    public function sendWebsiteRequestAction(Request $request)
    {
        $handshake = new Handshake();

        $form = $this->createForm(HandshakeType::class, $handshake);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $requestId = $this->get('handshake_manager')->createWebsiteRequest($handshake);

            if ($requestId) {

                $handshake->setRequestId($requestId);

                // Save Handshake to DB or something...

                $this->addFlash('success', 'Yay! You sent a handshake request. ID: '.$requestId);
            } else {

                $this->addFlash('error', 'It looks like there was an issue with your request...');
            }

            return $this->redirectToRoute('hellosign_website');
        }

        return $this->render('hellosign/send_request.html.twig', array(
            'form' => $form->createView()
        ));
    }
}