<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Acme\DemoBundle\Entity\Activity;

class RandomActivitiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('random:activity')
             ->setDescription('Generate Random Activities')
             ->addArgument(
                 'user_id',
                 InputArgument::REQUIRED,
                 'Who do you want to add activities to?'
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('user_id');
        $doctrine = $this->getContainer()->get('doctrine');
        $userRepo = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $userRepo->find($userId);
        $activityChoices = array('Github', 'Twitter', 'Facebook', 'Linked In', 'GMail', 'Google', 'Practo');
        $deviceChoices = array('Chrome Linux', 'Chrome Windows', 'Firefox Linux', 'Android');

        $em = $doctrine->getManager();
        $entries = 30;
        while ($entries > 0) {
            $activity = new Activity();
            $startTime = time() + rand(0, 100 * $entries);
            $randomDuration = rand(30, 500);
            $activity->setActivity($activityChoices[rand(0, count($activityChoices) - 1)]);
            $activity->setStartTime($startTime);
            $activity->setEndTime($startTime + $randomDuration);
            $activity->setUser($user);
            $activity->setDevice($deviceChoices[rand(0, count($deviceChoices) - 1)]);
            $em->persist($activity);
            $entries -= 1;
        }

        $em->flush();
    }
}
