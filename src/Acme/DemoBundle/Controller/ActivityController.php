<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Acme\DemoBundle\Entity\Activity;

class ActivityController extends Controller
{
    public function getActivityAction()
    {
        $response = new Response();
        $response->setContent('<html><body><h1>Hello world!</h1></body></html>');
        $response->setStatusCode(200);

        $response->send();
    }

    public function postActivityAction()
    {
        $postData = $this->getRequest()->request->all();
        $doctrine = $this->get('doctrine');
        $em = $doctrine->getManager();

        $activity = new Activity;
        $activity->setStartTime($postData['start_time']);
        $activity->setEndTime($postData['end_time']);
        $activity->setActivity($postData['activity']);
        $activity->setDevice($postData['device']);
        $token = $postData['token'];

        $er = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $er->findOneBy(array('token' => $token));
        $activity->setUser($user);
        $em->persist($activity);
        $em->flush();

        return $activity->serialise();
    }
}