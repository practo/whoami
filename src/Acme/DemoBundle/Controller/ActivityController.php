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

    public function getActivitiesSleepingtimeWeekAction()
    {
        $getParams = $this->getRequest()->query->all();
        $doctrine = $this->get('doctrine');
        $repo = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $repo->findOneByToken($getParams['token']);

        $doctrine = $this->get('doctrine');
        $em = $doctrine->getManager();

        $now = new \DateTime();
        $lastSunday = new \DateTime('last sunday midnight');
        if ($now > date_add($lastSunday, new \DateInterval('P7D'))) {
            $lastSunday = new \DateTime('this sunday midnight');
        }

        $aggregationUnit = 'week';
        $startTime = $lastSunday->getTimestamp();

        $now = new \DateTime();
        $now->setTime(16, 30);
        $week = new \DateTime();
        $week->setTime(04, 30);
        $week->sub(new \DateInterval('P07D'));

        $qb = $em->createQueryBuilder();
        $activities = $em->getConnection()->executeQuery("
            SELECT a.start_time, a.end_time
            FROM activities a
            WHERE ((a.start_time - :weekStart) % 86400 > 16 * 3600 + 30 * 60
             OR (a.start_time - :weekStart) % 86400 < 4 * 3600 + 30 * 60)
              AND a.user_id = :userId", array(
                'weekStart' => $startTime,
                'userId' => $user->getId()
            ));

        $correction = (24 - 16.5) * 3600;
        $segments = array(array(0, 12 * 3600));
        $activities = $activities->fetchAll();
        foreach ($activities as $activity) {
            $correctedAST = (($activity['start_time'] - $startTime) % 86400 + $correction) % 86400;
            $correctedAET = (($activity['end_time'] - $startTime) % 86400 + $correction) % 86400;
            foreach ($segments as $idx => $segment) {
                if ($correctedAST > $segment[1]) {
                    continue;
                }
                if ($correctedAET < $segment[0]) {
                    continue;
                }
                if ($correctedAST > $segment[0]){
                    if ($correctedAET < $segment[1]) {
                        unset($segments[$idx]);
                        $segments[] = array($segment[0], $correctedAST);
                        $segments[] = array($correctedAET, $segment[1]);
                        break;
                    } else {
                        // activity overlap to the segment on right
                        $segment[1] = $correctedAST;
                        break;
                    }
                } else {
                    if ($correctedAET > $segment[0]) {
                        $segment[0] = $correctedAET;
                        break;
                    }
                }
            }
        }
        if (count($segments) == 1) {
            // No activity
        }
        $max = array(-1);
        foreach ($segments as $segment) {
            $diff = $segment[1] - $segment[0];
            if ($diff > $max[0]) {
                $max = array($diff, $segment);
            }
        }

        $correctMax = array(
            (86400 - $correction + $max[1][0]) % 86400,
            (86400 - $correction + $max[1][1]) % 86400
        );

        return array('sleep_time' => $correctMax);
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

    public function getActivityGroupAction()
    {
        $getParams = $this->getRequest()->query->all();
        $doctrine = $this->get('doctrine');
        $repo = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $repo->findOneByToken($getParams['token']);
        $em = $doctrine->getManager();

        $now = new \DateTime();
        $week = new \DateTime();
        $week->sub(new \DateInterval('P07D'));

        $qb = $em->createQueryBuilder()
                  ->select('SUM(a.endTime - a.startTime) as total_time, a.activity')
                  ->from('AcmeDemoBundle:Activity', 'a')
                  ->where('a.user = :userId')
                  ->andWhere('a.startTime <= :start')
                  ->andWhere('a.endTime >= :end')
                  ->groupBy('a.activity')
                  ->setParameters(array('userId' => $user->getId(),
                    'start' => $now->getTimestamp(), 'end' => $week->getTimestamp()));
        return $qb->getQuery()->getResult();
    }

    public function getActivityAndroidgroupAction()
    {
        $getParams = $this->getRequest()->query->all();
        $doctrine = $this->get('doctrine');
        $repo = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $repo->findOneByToken($getParams['token']);
        $em = $doctrine->getManager();

        $now = new \DateTime();
        $week = new \DateTime();
        $week->sub(new \DateInterval('P07D'));

        $qb = $em->createQueryBuilder()
                  ->select('SUM(a.endTime - a.startTime) as total_time, a.activity')
                  ->from('AcmeDemoBundle:Activity', 'a')
                  ->where('a.user = :userId')
                  ->andWhere('a.startTime <= :start')
                  ->andWhere('a.endTime >= :end')
                  ->andWhere("a.device = 'android'")
                  ->groupBy('a.activity')
                  ->setParameters(array('userId' => $user->getId(),
                    'start' => $now->getTimestamp(), 'end' => $week->getTimestamp()));
        return $qb->getQuery()->getResult();
    }
}