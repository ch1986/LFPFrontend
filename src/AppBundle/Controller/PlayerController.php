<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Entity\Player;
use AppBundle\Form\PlayerType;

class PlayerController extends Controller
{
    /**
     * @Route("/players/{clubId}", name="AppBundle_player_listing", requirements={"clubId": "\d+"})
     */
    public function listAction(Request $request, $clubId)
    {
        $client = $this->container->get('guzzle.client.lfp_api');
        $url =  '/lfpbackend/web/app_dev.php/players/'.$clubId;
        
        $headers = array ();
        
        $response = $client->get($url, array('verify' => false, 'headers' => $headers, 'exceptions' => false));
        $responseContent = json_decode($response->getBody(), true);
        
        $url =  '/lfpbackend/web/app_dev.php/club/'.$clubId;
        
        $response = $client->get($url, array('verify' => false, 'headers' => $headers, 'exceptions' => false));
        $responseContent2 = json_decode($response->getBody(), true);
        
        return $this->render('AppBundle:Player:listing.html.twig', array('players' => $responseContent, 'club' => $responseContent2));
    }
    
    /**
     * @Route("/player/new/{clubId}", name="AppBundle_player_new", requirements={"clubId": "\d+"})
     */
    public function newAction(Request $request, $clubId)
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);
            
            $client = $this->container->get('guzzle.client.lfp_api');
            $url =  '/lfpbackend/web/app_dev.php/player/'.$clubId;

            $headers = array ();

            $jsonPlayer = $serializer->serialize($player, 'json');
            
            $response = $client->post($url, array('verify' => false, 'headers' => $headers, 'body' => $jsonPlayer, 'exceptions' => false));
            $responseContent = json_decode($response->getBody(), true);

            return $this->redirect($this->generateUrl('AppBundle_player_listing', array('clubId' => $clubId)));
        }
        return $this->render('AppBundle:Player:new.html.twig', array('form' => $form->createView(), 'clubId' => $clubId));
    }
}
