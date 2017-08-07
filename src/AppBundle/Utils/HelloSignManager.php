<?php

namespace AppBundle\Utils;

use AppBundle\Model\Handshake;
use HelloSign\Client;
use HelloSign\EmbeddedSignatureRequest;
use HelloSign\SignatureRequest;
use Monolog\Logger;

class HelloSignManager
{
    private $helloSignApiKey;
    private $helloSignClientId;
    private $logger;

    public function __construct($helloSignApiKey, $helloSignClientId, Logger $logger)
    {
        $this->helloSignApiKey = $helloSignApiKey;
        $this->helloSignClientId = $helloSignClientId;
        $this->logger = $logger;
    }

    /**
     * Note: This example only allows for 1 signer.
     *
     * @param Handshake $handshake
     * @return mixed
     */
    public function createIntegratedRequest(Handshake $handshake)
    {
        try {

            $client = new Client('c183c7dab7732e58bb543881f6ed21b31b62855a51ecd26f7b4f91fc514b3983');
            $request = new SignatureRequest();
            $request->enableTestMode();
            $request->setSubject('Embedded Handshake signature request');
            $request->setMessage($handshake->getMessage());
            $request->addSigner($handshake->getEmail(), $handshake->getName());
            $request->addFile('HandShake.pdf');

            $client_id = $this->helloSignClientId;
            $embedded_request = new EmbeddedSignatureRequest($request, $client_id);
            $response = $client->createEmbeddedSignatureRequest($embedded_request);

        } catch (\Exception $e) {

            // Log error
            $this->logger->error('HelloSign Error: '.$e->getMessage());

            return false;
        }

        return $response;
    }

    public function createWebsiteRequest(Handshake $handshake)
    {
        try {
            $client = new Client($this->helloSignApiKey);
            $request = new SignatureRequest();
            $request->enableTestMode();
            $request->setTitle('Handshake Signature');
            $request->setSubject('Let\'s sign our handshake');
            $request->setMessage($handshake->getMessage());
            $request->addSigner($handshake->getEmail(), $handshake->getName());
            $request->addFile('Handshake.pdf');
            $response = $client->sendSignatureRequest($request);

            // Signature Request ID
            $requestId = $response->getId();

        } catch (\Exception $e) {

            // Log error
            $this->logger->error('HelloSign Error: '.$e->getMessage());

            return false;
        }

        return $requestId;
    }

    public function getEmbeddedSignatureUrl($signatureId)
    {
        try {

            $client = new Client($this->helloSignApiKey);
            $response = $client->getEmbeddedSignUrl($signatureId);

            $signUrl = $response->getSignUrl();

        } catch (\Exception $e) {

            // Log error
            $this->logger->error('HelloSign Error: '.$e->getMessage());

            return false;
        }

        return $signUrl;
    }
}
