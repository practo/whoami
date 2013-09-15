<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Acme\DemoBundle\Entity\LocationSummary;

class LocationSummaryCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('locationsummary')
             ->setDescription('Generate location summary');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->getContainer()->get("leezy.pheanstalk");
        $doctrine = $this->getContainer()->get('doctrine');

        while(true) {
            $job = $pheanstalk->watch('whoamiLocationUpdate')->reserve();

            $now = new \DateTime();
            $lastMonday = new \DateTime('last monday midnight');
            if ($now > date_add($lastMonday, new \DateInterval('P7D'))) {
                $lastMonday = new \DateTime('this monday midnight');
            }
            $location = json_decode($job->getData(), true);
            $userRepo = $doctrine->getRepository('AcmeDemoBundle:User');
            $user = $userRepo->find($location['user_id']);
            $em = $doctrine->getManager();
            $lsRepo = $doctrine->getRepository('AcmeDemoBundle:LocationSummary');
            $luRepo = $doctrine->getRepository('AcmeDemoBundle:LocationUpdate');
            if ($location['hash'] == $user->getHomeHash()) {
                $category = 'home';
            } else if ($location['hash'] == $user->getWorkHash()) {
                $category = 'work';
            } else {
                $em = $doctrine->getManager();
                $qb = $em->createQueryBuilder()
                          ->select('COUNT(l) l_count')
                          ->from('AcmeDemoBundle:LocationUpdate', 'l')
                          ->where('l.user = :userId')
                          ->andWhere('l.timestamp <= :start')
                          ->andWhere('l.timestamp >= :end')
                          ->andWhere('l.hash = :thisHash')
                          ->setParameters(array(
                            'userId' => $user->getId(),
                            'start' => $now->getTimestamp(),
                            'end' => $lastMonday->getTimestamp(),
                            'thisHash' => $location['hash']
                          ));
                $count = intval($qb->getQuery()->getSingleScalarResult());
                if ($count>5) {
                    $category = 'other';
                } else {
                    $category = 'travel';
                }
            }

            $locationSummary = $lsRepo->findOneBy(array(
                        'name' => $category,
                        'startTime' => $lastMonday->getTimestamp(),
                        'aggregationUnit' => 'week',
                        'user' => $user
            ));

            if (!$locationSummary) {
                $locationSummary = new LocationSummary();
                $locationSummary->setName($category);
                $locationSummary->setStartTime($lastMonday->getTimestamp());
                $locationSummary->setAggregationUnit('week');
                $locationSummary->setDurationSeconds(0);
                $locationSummary->setUser($user);
                $em->persist($locationSummary);
            }
            $justBeforeLocationTs = $luRepo->createQueryBuilder('l')
                                          ->where('l.user = :userId')
                                          ->andWhere('l.timestamp < :thisOne')
                                          ->orderBy('l.timestamp')
                                          ->setMaxResults(1)
                                          ->setParameters(array('userId' => $user,
                                            'thisOne' => $location['timestamp']));
            $data = $justBeforeLocationTs->getQuery()->getOneOrNullResult();
            if ($data) {
                $locationSummary->setDurationSeconds(
                    $locationSummary->getDurationSeconds() + $location['timestamp'] -
                        $data->getTimestamp()
                );
            }
            $em->flush();
            $pheanstalk->delete($job);
        }
    }
}