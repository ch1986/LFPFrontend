<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClubController extends Controller
{
    /**
     * @Route("/", name="AppBundle_club_listing")
     */
    public function indexAction(Request $request)
    {
        $client = $this->container->get('guzzle.client.lfp_api');
        $url =  '/lfpbackend/web/app_dev.php/club';
        
        $headers = array ();
        
        $response = $client->get($url, array('verify' => false, 'headers' => $headers, 'exceptions' => false));
        $responseContent = json_decode($response->getBody(), true);
        
        return $this->render('AppBundle:Club:listing.html.twig', array('clubs' => $responseContent));
    }
}
