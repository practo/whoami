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
    public function getActivitiesAction()
    {
        $getParams = $this->getRequest()->query->all();
        $doctrine = $this->get('doctrine');
        $repo = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $repo->findOneByToken($getParams['token']);
        $returnData = array();
        $activities = $user->getActivities();
        foreach ($activities as $activity) {
            $returnData[] = $activity->serialise();
        }

        return $returnData;
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