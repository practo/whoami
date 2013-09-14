<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;

class WhoamiController extends Controller
{
    public function getWhoamiAction()
    {
        $response = new Response();
        $response->setContent('<html><body><h1>Hello world!</h1></body></html>');
		$response->setStatusCode(200);

		$response->send();
    }
}
