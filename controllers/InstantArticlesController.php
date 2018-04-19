<?php

namespace Plugins\Accio\InstantArticles\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MainPluginsController;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Plugins\Accio\InstantArticles\Models\InstantArticles;

class InstantArticlesController extends MainPluginsController{

    /**
     * Facebook Login callback
     *
     * @param Request $request
     * @return array
     */
    public function loginCallback(Request $request){
        $instance = InstantArticles::getInstance($request->app_id, $request->app_secret, $request->response['authResponse']['accessToken']);

        if ($instance){
            $facebookAccount = $instance->get('/me/accounts');

            // Validate Page
            $pageExist = false;
            $pageList = $facebookAccount->getGraphEdge()->asArray();
            foreach($pageList as $page){
                if($page['id'] == $request->page_id){
                    $pageExist = true;
                    break;
                }
            }

            if(!$pageExist) {
                return $this->response("It seems like don't have access on this Page! ".InstantArticles::$instanceException->getMessage(), 500);
            }

            // Exchange short-lived for a long-lived access token
            $oAuth2Client = $instance->getOAuth2Client();
            $accessToken = $oAuth2Client->getLongLivedAccessToken($request->response['authResponse']['accessToken']);

            // Save app & login data
            $instantData = InstantArticles::first();
            if(!$instantData){
                $instantData = new InstantArticles();
            }

            $instantData->appID = $request->app_id;
            $instantData->pageID = $request->page_id;
            $instantData->appSecret = $request->app_secret;
            $instantData->accessToken = $accessToken;
            $instantData->save();

            return $this->response( 'Instant articles successfully connected!', 200);
        }

        return $this->response(InstantArticles::$instanceException->getMessage(), 500);
    }

    /**
     * Save configuration
     *
     * @param Request $request
     * @return array
     */
    public function saveConfiguration(Request $request){
        $instance = InstantArticles::getInstance();
        if($instance) {
            $instantData = InstantArticles::first();
            if ($instantData) {
                $instantData->style = $request->style;
                $instantData->customTransformerRules = $request->customTransformerRules;
                $instantData->copyright = $request->copyright;
                $instantData->metaCode = $request->metaCode;
                $instantData->rtl = $request->rtl;
                $instantData->developmentMode = $request->developmentMode;
                $instantData->showComments = $request->showComments;
                $instantData->showLikes = $request->showLikes;
                $instantData->showRelatedPosts = $request->showRelatedPosts;
                $instantData->adType = $request->adType;
                $instantData->adPlacementIDs = $request->adPlacementIDs;
                $instantData->adIframeURL = $request->adIframeURL;
                $instantData->adCustomCode = $request->adCustomCode;
                $instantData->save();

                return $this->response('Configuration updated successfully!', 200);
            }else{
                return $this->response('Instant Articles configuration could not be saved!', 500);
            }
        }else{
            return $this->response(InstantArticles::$instanceException->getMessage(), 500);
        }
    }

    /**
     * Get all plugin data
     * @return array
     */
    public function getDetails(){
        $instance = InstantArticles::getInstance();

        if($instance) {
            return  [
                'code' => 200,
                'user' => InstantArticles::getLoggedInUser(),
                'data' => InstantArticles::$pluginData,
            ];
        }else{
//            return $this->response( InstantArticles::$instanceException->getMessage(), 500);
        }
    }
}