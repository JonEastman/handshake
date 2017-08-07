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
    public function sendIntegratedRequestAction(Request $request)
    {
        $handshake = new Handshake();

        $form = $this->createForm(HandshakeType::class, $handshake);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $response = $this->get('hellosign_manager')->createIntegratedRequest($handshake);

            if ($response) {

                // Typically this would be saved in database until the requested user attempts to sign...
                $handshake->setRequestId($response->getId());

                // Signature Request ID
                $signatures = $response->getSignatures();

                // First Signature in collection
                $signature = $signatures[0];

                $signatureId = $signature->signature_id;

                $this->addFlash('success', 'Yay! You sent a handshake request');

                return $this->redirectToRoute('hellosign_view_embedded', array('id' => $signatureId));

            } else {

                $this->addFlash('error', 'It looks like there was an issue with your request...');
            }
        }

        return $this->render('hellosign/send_request.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * This is for example purposes ONLY
     *
     * @Route("/hello-sign/integrated/view/{id}", name="hellosign_view_embedded")
     */
    public function viewIntegratedRequestAction($id)
    {
        // Get Embedded Signature ID
        $signatureUrl = $this->get('hellosign_manager')->getEmbeddedSignatureUrl($id);

        $clientId = $this->getParameter('hello_sign_client_id');

        $redirectUrl = $this->generateUrl('hellosign_embedded_signed', array(), true);

        return $this->render('hellosign/embedded_request.html.twig', array(
            'signatureUrl'  => $signatureUrl,
            'clientId'      => $clientId,
            'redirectUrl'   => $redirectUrl
        ));
    }

    /**
     * Redirects User back to Homepage with Flash Message
     *
     * @Route("/hello-sign/integrated/signed", name="hellosign_embedded_signed")
     */
    public function integratedSignedAction()
    {
        $this->addFlash('success', 'You successfully signed the Embedded Request');

        return $this->redirectToRoute('homepage');
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

            $requestId = $this->get('hellosign_manager')->createWebsiteRequest($handshake);

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