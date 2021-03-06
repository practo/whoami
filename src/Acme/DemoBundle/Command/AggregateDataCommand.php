<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Acme\DemoBundle\Entity\ActivitySummary;

class AggregateDataCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('aggregatedata')
             ->setDescription('Generate aggregated data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->getContainer()->get("leezy.pheanstalk");
        $doctrine = $this->getContainer()->get('doctrine');

        while(true) {
            $job = $pheanstalk->watch('whoamiActivity')->reserve();

            $now = new \DateTime();
            $lastMonday = new \DateTime('last monday midnight');
            if ($now > date_add($lastMonday, new \DateInterval('P7D'))) {
                $lastMonday = new \DateTime('this monday midnight');
            }
            $activity = json_decode($job->getData(), true);
            $userRepo = $doctrine->getRepository('AcmeDemoBundle:User');
            $user = $userRepo->find($activity['user_id']);

            $em = $doctrine->getManager();
            $category = $this->categorise($activity);
            $this->updateLocationActivitySummary(null, $category, $lastMonday, $user, $activity);
            $luRepo = $doctrine->getRepository('AcmeDemoBundle:LocationUpdate');
            $latestLocation = $luRepo->createQueryBuilder('l')
                                     ->where('l.user = :user')
                                     ->orderBy('l.timestamp', 'DESC')
                                     ->setMaxResults(1)
                                     ->setParameter('user', $user)
                                     ->getQuery()
                                     ->getOneOrNullResult();
            if ($latestLocation) {
                if ($latestLocation->getHash() == $user->getHomeHash()) {
                    $this->updateLocationActivitySummary('home', $category, $lastMonday, $user, $activity);
                } else if ($latestLocation->getHash() == $user->getWorkHash()) {
                    $this->updateLocationActivitySummary('work', $category, $lastMonday, $user, $activity);
                }
            }
            $em->flush();
            $pheanstalk->delete($job);
        }
    }

    public function categorise($activity)
    {
        $name = $activity['activity'];
        $parts = explode('://', $name, 2);
        if (count($parts) > 1) {
            $name = $parts[1];
        }
        $name = explode('/', $name)[0];
        if (substr($name, 0, 4) === 'www.') {
            $name = substr($name, 4);
        }

        return $name;
    }

    protected function updateLocationActivitySummary($location, $category, $lastMonday, $user, $activity)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $asRepo = $doctrine->getRepository('AcmeDemoBundle:ActivitySummary');
        $em = $doctrine->getManager();
        $activitySummary = $asRepo->findOneBy(array(
            'name' => $category,
            'startTime' => $lastMonday->getTimestamp(),
            'aggregationUnit' => 'week',
            'user' => $user,
            'location' => $location
        ));

        if (!$activitySummary) {
            $activitySummary = new ActivitySummary();
            $activitySummary->setName($category);
            $activitySummary->setLocation($location);
            $activitySummary->setStartTime($lastMonday->getTimestamp());
            $activitySummary->setAggregationUnit('week');
            $activitySummary->setDurationSeconds(0);
            $activitySummary->setUser($user);
            $em->persist($activitySummary);
        }
        $activitySummary->setDurationSeconds(
            $activitySummary->getDurationSeconds() +  intval($activity['end_time']) - intval($activity['start_time'])
        );
    }
}