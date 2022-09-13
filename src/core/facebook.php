<?php namespace Core;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

class FacebookClient
{
    public function connect()
    {
        global $config;

        $fb = new Facebook([
            'app_id' => $config['social_facebook_app_id'],
            'app_secret' => $config['social_facebook_app_secret'],
            'default_graph_version' => 'v8.0'
        ]);

        try {
          // Returns a `Facebook\FacebookResponse` object
          $response = $fb->get(
            '/211039099516918/photos?type=uploaded',
            'EAAJxoEZAQsmgBABoZBMt1CwBbgVmcU613CfgNNycs0WJprzfPpjoVMwabZC6f6cxBu2nBCXzdZBm0hgpZCCvRPjDTvD8495xUL3l4ldRSk6q7LtCHfoD6wywJRvyNi4TumxnsZBSgFb1FahU7tbW1dmyHzMV3fST3ziVvBiC0qqQZDZD'
          );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }
        $graphNode = $response->getGraphNode();

        print_r($graphNode);
        exit;
    }
}
