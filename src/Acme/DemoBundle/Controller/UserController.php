<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;

class UserController extends Controller
{
    public function getUserAction()
    {
        $response = new Response();
        $response->setContent('<html><body><h1>Hello world!</h1></body></html>');
		$response->setStatusCode(200);

		$response->send();
    }

    public function postUserAction()
    {
    	$postData = $this->getRequest()->request->all();
    	var_dump($postData);die;
    }
}