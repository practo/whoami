<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Acme\DemoBundle\Entity\Activity;

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
            $asRepo = $doctrine->getRepository('AcmeDemoBundle:ActivitySummary');
            $category = $this->categorise($activity);
            $activitySummary = $asRepo->findOneBy(array(
                'name' => $category,
                'startTime' => $lastMonday->getTimestamp(),
                'aggregationUnit' => 'week',
                'user' => $user
            ));

            if (!$activitySummary) {
                $activitySummary = new ActivitySummary();
                $activitySummary->setName($category);
                $activitySummary->setLocation('home');
                $activitySummary->setStartTime($lastMonday->getTimestamp());
                $activitySummary->setAggregationUnit('week');
                $activitySummary->setDurationSeconds(0);
                $activitySummary->setUser($user);
                $em->persist($activitySummary);
            }
            $activitySummary->setDurationSeconds(
                $activitySummary->getDurationSeconds() +  intval($activity['end_time']) - intval($activity['start_time'])
            );
            $em->flush();
            $pheanstalk->delete($job);
        }
    }

    public function categorise($activity)
    {
        return $activity['activity'];
    }
}