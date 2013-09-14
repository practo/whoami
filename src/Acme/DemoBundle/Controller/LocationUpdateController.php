<?php

namespace Acme\DemoBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Acme\DemoBundle\Entity\User;

class LocationUpdateController extends Controller
{
    public function getLocationupdatesAction()
    {
        $getParams = $this->getRequest()->query->all();
        $doctrine = $this->get('doctrine');
        $repo = $doctrine->getRepository('AcmeDemoBundle:User');
        $user = $repo->findOneByToken($getParams['token']);
        $returnData = array();
        $locationUpdates = $user->getLocationUpdates();
        foreach ($locationUpdates as $locationUpdate) {
            $returnData[] = $locationUpdate->serialise();
        }

        return $returnData;
    }

    public function postLocationupdatesAction()
    {
        $postData = $this->getRequest()->request->all();
        $doctrine = $this->get('doctrine');
        $repo = $doctrine->getEntityRepository('AcmeDemoBundle:User');
        $em = $doctrine->getManager();
        $user = $repo-findOneByToken($postData['token']);
        $locationUpdate = new LocationUpdate;
        $locationUpdate->setUser($user);
        $locationUpdate->setTimestamp($postData['timestamp']);
        $locationUpdate->setLatitude($postData['latitude']);
        $locationUpdate->setLongitude($postData['longitude']);
        $em->persist($locationUpdate);
        $em->flush();

        return $locationUpdate->serialise();
    }
}
