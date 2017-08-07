<?php

namespace AppBundle\Utils;

use AppBundle\Model\Handshake;
use HelloSign\Client;
use HelloSign\SignatureRequest;
use Monolog\Logger;

class HelloSignManager
{
    private $helloSignApiKey;
    private $logger;

    public function __construct($helloSignApiKey, Logger $logger)
    {
        $this->helloSignApiKey = $helloSignApiKey;
        $this->logger = $logger;
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
}