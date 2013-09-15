<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Acme\DemoBundle\Entity\Activity;

class DashboardController extends Controller
{
    public function getCurrentweeksummaryAction()
    {
        $now = new \DateTime();
        $lastMonday = new \DateTime('last monday midnight');
        if ($now > date_add($lastMonday, new \DateInterval('P7D'))) {
            $lastMonday = new \DateTime('this monday midnight');
        }

        $aggregationUnit = 'week';
        $startTime = $lastMonday->getTimestamp();

        $getParams = $this->getRequest()->query->all();
        $doctrine = $this->get('doctrine');
        $userRepo = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $userRepo->findOneByToken($getParams['token']);
        $returnData = array(
            'location_summary' => array(),
            'activity_summary' => array(),
            'activity_home_summary' => array(),
            'activity_work_summary' => array(),
        );
        $lsRepo = $doctrine->getRepository('AcmeDemoBundle:LocationSummary');
        $entities = $lsRepo->findBy(array(
            'user' => $user,
            'aggregationUnit' => 'week',
            'startTime' => $startTime
        ));
        foreach ($entities as $entity) {
            $returnData['location_summary'][] = $entity->serialise();
        }
        $asRepo = $doctrine->getRepository('AcmeDemoBundle:ActivitySummary');
        $entities = $asRepo->findBy(array(
            'user' => $user,
            'aggregationUnit' => 'week',
            'startTime' => $startTime,
            'location' => null,
        ));
        foreach ($entities as $entity) {
            $returnData['activity_summary'][] = $entity->serialise();
        }
        $entities = $asRepo->findBy(array(
            'user' => $user,
            'aggregationUnit' => 'week',
            'startTime' => $startTime,
            'location' => 'home',
        ));
        foreach ($entities as $entity) {
            $returnData['activity_home_summary'][] = $entity->serialise();
        }
        $entities = $asRepo->findBy(array(
            'user' => $user,
            'aggregationUnit' => 'week',
            'startTime' => $startTime,
            'location' => 'work',
        ));
        foreach ($entities as $entity) {
            $returnData['activity_work_summary'][] = $entity->serialise();
        }

        return $returnData;
    }
}
