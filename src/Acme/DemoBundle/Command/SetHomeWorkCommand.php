<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Acme\DemoBundle\Entity\Activity;

class SetHomeWorkCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('sethomework')
             ->setDescription('set home work');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');

        $userRepo = $doctrine->getRepository('AcmeDemoBundle:User');
        $users = $userRepo->findAll();

        $now = new \DateTime();
        $lastMonday = new \DateTime('last monday midnight');
        if ($now->format('N') == 1) {
            $lastMonday = new \DateTime('this monday midnight');
        }

        $em = $doctrine->getManager();
        foreach ($users as $user) {
            $qb = $em->createQueryBuilder()
                      ->select('COUNT(l) l_count, l.hash')
                      ->from('AcmeDemoBundle:LocationUpdate', 'l')
                      ->where('l.user = :userId')
                      ->andWhere('l.timestamp <= :start')
                      ->andWhere('l.timestamp >= :end')
                      ->setParameters(array(
                        'userId' => $user->getId(),
                        'start' => $now->getTimestamp(),
                        'end' => $lastMonday->getTimestamp()
                      ))
                      ->groupBy('l.hash')
                      ->orderBy('l_count', 'DESC')
                      ->setMaxResults(2);
            $locationUpdates = $qb->getQuery()->getResult();

            if (count($locationUpdates) < 2) {
                continue; // not enough data
            }

            $sts = $this->getSleepTimings($lastMonday->getTimestamp(), $user);
            $sleepCount = array();
            if ($sts[0] > $sts[1]) {
                // Sleep timings wrap around midnight
                foreach ($locationUpdates as $locationUpdate) {
                    $sleepCount[] = $em->getConnection()->executeQuery("
                        SELECT COUNT(l.id) l_count
                        FROM location_updates l
                        WHERE ((l.timestamp - :weekStart) % 86400 > {$sts[0]}
                         OR (l.timestamp - :weekStart) % 86400 < {$sts[1]})
                          AND l.user_id = :userId
                          AND l.hash = :hash", array(
                            'weekStart' => $lastMonday->getTimestamp(),
                            'userId' => $user->getId(),
                            'hash' => $locationUpdate['hash'],
                    ));
                }
            } else {
                foreach ($locationUpdates as $locationUpdate) {
                    $sleepCount[] = $em->getConnection()->executeQuery("
                        SELECT COUNT(l.id) l_count
                        FROM location_updates l
                        WHERE ((l.timestamp - :weekStart) % 86400 > {$sts[0]}
                         AND (l.timestamp - :weekStart) % 86400 < {$sts[1]})
                          AND l.user_id = :userId
                          AND l.hash = :hash", array(
                            'weekStart' => $lastMonday->getTimestamp(),
                            'userId' => $user->getId(),
                            'hash' => $locationUpdate['hash'],
                    ));
                }
            }
            if ($sleepCount[0] > $sleepCount[1]) {
                $user->setHomeHash($locationUpdates[0]['hash']);
                $user->setWorkHash($locationUpdates[1]['hash']);
            } else {
                $user->setHomeHash($locationUpdates[1]['hash']);
                $user->setWorkHash($locationUpdates[0]['hash']);
            }

            $em->flush();
        }
    }

    protected function getSleepTimings($startTime,  $user)
    {
        $now = new \DateTime();
        $now->setTime(16, 30);
        $week = new \DateTime();
        $week->setTime(04, 30);
        $week->sub(new \DateInterval('P07D'));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
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

        return $correctMax;
    }
}