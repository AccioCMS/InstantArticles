<?php

namespace Plugins\Accio\InstantArticles\Models;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
Use Illuminate\Database\Eloquent\Model;

class InstantArticles extends Model{
    protected $table = "accio_instant_articles";
    public static $instance;
    public static $instanceException;
    public static $pluginData;

    /**
     * Get Facebook instance
     *
     * @param integer $app_id
     * @param string $app_secret
     * @param string $access_token
     * @return bool|Facebook Returns false if an error occurred, Facebook instance otherwise
     * @throws FacebookResponseException|FacebookSDKException
     */
    public static function getInstance($app_id = null, $app_secret = null, $access_token = null){
        if(self::$instance){
            return self::$instance;
        }

        // get app data from DB if one of the properties is empty
        if (empty($app_id) || empty($app_secret) || empty($access_token)){
            $pluginData = self::all()->first();
            if($pluginData){
                self::$pluginData = $pluginData;
                $app_id = $pluginData->appID;
                $app_secret = $pluginData->appSecret;
                $access_token = $pluginData->accessToken;
            }
        }

        if (!empty($app_id) && !empty($app_secret) && !empty($access_token)) {

            $fb = new Facebook([
                'app_id' => $app_id,
                'app_secret' => $app_secret,
            ]);

            $fb->setDefaultAccessToken($access_token);

            try {
                $fb->get('/me');
            } catch (FacebookResponseException $e) {
                // When Graph returns an error
                self::$instanceException = $e;
                return false;
            } catch (FacebookSDKException $e) {
                // When validation fails or other local issues
                self::$instanceException = $e;
                return false;
            }

            self::$instance = $fb;
            return $fb;
        }
        return false;
    }

    /**
     *
     * Get logged in user data from Facebook
     * @return object|null
     * @throws FacebookResponseException
     * @throws FacebookSDKException
     *
     */
    public static function getLoggedInUser(){
        $instance = self::getInstance();
        if($instance){
            $response = $instance->get('/me');
            return $response->getGraphUser();
        }

        return null;
    }
}