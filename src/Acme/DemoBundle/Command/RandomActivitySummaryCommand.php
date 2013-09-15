<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Acme\DemoBundle\Entity\ActivitySummary;

class RandomActivitySummaryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('random:activity_summary')
             ->setDescription('Generate Random Activity Summary')
             ->addArgument(
                 'user_id',
                 InputArgument::REQUIRED,
                 'Who do you want to set activity summary to?'
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('user_id');
        $doctrine = $this->getContainer()->get('doctrine');
        $userRepo = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $userRepo->find($userId);

        $em = $doctrine->getManager();
        $categories = array('Productivity', 'Entertainment', 'Travel',
            'Networking', 'Sleep');
        $now = new \DateTime();
        $lastMonday = new \DateTime('last monday midnight');
        if ($now > date_add($lastMonday, new \DateInterval('P7D'))) {
            $lastMonday = new \DateTime('this monday midnight');
        }
        foreach ($categories as $category) {
            $randomDuration = rand(500, 86400 * 7 / count($categories));
            $activitySummary = new ActivitySummary();
            $activitySummary->setName($category);
            $activitySummary->setLocation(null);
            $activitySummary->setStartTime($lastMonday->getTimestamp());
            $activitySummary->setAggregationUnit('week');
            $activitySummary->setDurationSeconds($randomDuration);
            $activitySummary->setUser($user);
            $em->persist($activitySummary);
        }
        foreach ($categories as $category) {
            if ($category == 'Sleep') {
                $randomDuration = rand(20000, 86400 * 7 / count($categories));
            } else {
                $randomDuration = rand(500, 86400 * 7 / count($categories));
            }
            $activitySummary = new ActivitySummary();
            $activitySummary->setName($category);
            $activitySummary->setLocation('home');
            $activitySummary->setStartTime($lastMonday->getTimestamp());
            $activitySummary->setAggregationUnit('week');
            $activitySummary->setDurationSeconds($randomDuration);
            $activitySummary->setUser($user);
            $em->persist($activitySummary);
        }
        $categories = array_diff($categories, array('Sleep'));
        foreach ($categories as $category) {
            $randomDuration = rand(500, 86400 * 7 / count($categories));
            $activitySummary = new ActivitySummary();
            $activitySummary->setName($category);
            $activitySummary->setLocation('work');
            $activitySummary->setStartTime($lastMonday->getTimestamp());
            $activitySummary->setAggregationUnit('week');
            $activitySummary->setDurationSeconds($randomDuration);
            $activitySummary->setUser($user);
            $em->persist($activitySummary);
        }

        $em->flush();
    }
}
