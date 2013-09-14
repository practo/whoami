<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Acme\DemoBundle\Entity\User;

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
    	$doctrine = $this->get('doctrine');
    	//$repo = $doctrine->getEntityRepository('AcmeDemoBundle:User');
    	$em = $doctrine->getManager();
    	$user = new User;
    	$user->setName($postData['name']);
    	$user->setEmail($postData['email']);
    	$token = $this->getToken($postData['name'], $postData['email']);
    	$user->setToken($token);
     	$em->persist($user);
     	$em->flush();

     	return $user->serialise();
    }

    protected function getToken($name, $email)
    {
    	return substr(md5($name.$email), -6);
    }
}