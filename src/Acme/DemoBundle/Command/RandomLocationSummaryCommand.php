<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Acme\DemoBundle\Entity\LocationSummary;

class RandomLocationSummaryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('random:location_summary')
             ->setDescription('Generate Random Location Summary')
             ->addArgument(
                 'user_id',
                 InputArgument::REQUIRED,
                 'Who do you want to set location summary to?'
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('user_id');
        $doctrine = $this->getContainer()->get('doctrine');
        $userRepo = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $userRepo->find($userId);

        $em = $doctrine->getManager();
        $categories = array('Home', 'Work', 'Travel', 'Other');
        $now = new \DateTime();
        $lastMonday = new \DateTime('last monday midnight');
        if ($now > date_add($lastMonday, new \DateInterval('P7D'))) {
            $lastMonday = new \DateTime('this monday midnight');
        }
        foreach ($categories as $category) {
            $randomDuration = rand(500, 86400 * 7 / count($categories));
            $locationSummary = new LocationSummary();
            $locationSummary->setName($category);
            $locationSummary->setStartTime($lastMonday->getTimestamp());
            $locationSummary->setAggregationUnit('week');
            $locationSummary->setDurationSeconds($randomDuration);
            $locationSummary->setUser($user);
            $em->persist($locationSummary);
        }

        $em->flush();
    }
}
