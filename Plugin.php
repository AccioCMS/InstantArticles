<?php

namespace Plugins\Accio\InstantArticles;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Time;
use Facebook\InstantArticles\Elements\Ad;
use Facebook\InstantArticles\Elements\Analytics;
use Facebook\InstantArticles\Elements\Author;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Footer;
use Facebook\InstantArticles\Elements\Small;
use Facebook\InstantArticles\Transformer\Transformer;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Client\Client;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Accio\App\Interfaces\PluginInterface;
use Accio\App\Traits\PluginTrait;
use Mockery\Exception;
use Plugins\Accio\InstantArticles\Models\InstantArticles;
use Symfony\Component\Console\Command\Command;

class Plugin implements PluginInterface {
    use PluginTrait;

    /**
     * Instance Article object
     * @var InstantArticle
     */
    protected $instantArticle;

    /**
     * Transformer rules
     * @var object $transformer
     */
    protected $transformer;


    /**
     * Instant Article Configuration Data
     * @var object $configuration
     */
    protected $configuration;

    /**
     * Post Data
     * @var object $post
     */
    protected $post;

    /**
     * Instant Article Header
     * @var object $header
     */
    protected $header;

    /**
     * Lists the error from after the import article attempt
     * @var object $header
     */
    protected $publishingError;

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register(){
        // Saved Event
        Event::listen('post:saved', function($post){
            $this->store($post);
        });
    }

    /**
     * @param $post
     * @return bool|void
     * @throws \Facebook\Exceptions\FacebookResponseException
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function store($post){
        $proceedAsInstant = $this->getPanelData('Accio_InstantArticles_post','importInstantArticle');
        if($proceedAsInstant){
            $facebookInstance = InstantArticles::getInstance();
            if(!$facebookInstance) {
                $post->noty("error","Instant Article Instance Error: ".$this->publishingError, $this->getData()->namespaceWithDot());
                return false;
            }

            $published = $this->importInstantArticle($post, $proceedAsInstant);
            if(!$published) {
                $post->noty("error","Instant Article not published: ".$this->publishingError, $this->getData()->namespaceWithDot());
            }
        }
        return;
    }


    /**
     *  Do something after all plugins have been loaded,
     *
     * @return void
     */
    public function boot(){

    }

    /**
     * @param object $post
     * @param boolean $isPublished
     *
     * @return boolean
     */
    private function importInstantArticle($post, $isPublished){
        $this->configuration = InstantArticles::$pluginData;
        $this->post = $post;

        // Create Instant markup
        $this->enableQuiteLogger()
            ->loadRules()
            ->setHeader()
            ->setTitle()
            ->setAuthors()
            ->setCover()
            ->initInstantArticle()
            ->setStyle()
            ->setLikesOnMedia()
            ->setCommentsOnMedia()
            ->setContent()
            ->setAds()
            ->setMetaCode();

        $publish = $this->publish($isPublished);

        return $publish;
    }

    /**
     * Configures log to go through console.
     *
     * @return $this
     */
    private function enableQuiteLogger(){
        // Configures log to go through console.
        \Logger::configure(
            array(
                'rootLogger' => array(
                    'appenders' => array( 'facebook-instantarticles-transformer' ),
                ),
                'appenders' => array(
                    'facebook-instantarticles-transformer' => array(
                        'class' => 'LoggerAppenderConsole',
                        'threshold' => 'INFO',
                        'layout' => array(
                            'class' => 'LoggerLayoutSimple',
                        ),
                    ),
                    'facebook-instantarticles-client' => array(
                        'class' => 'LoggerAppenderConsole',
                        'threshold' => 'INFO',
                        'layout' => array(
                            'class' => 'LoggerLayoutSimple',
                        ),
                    ),
                    'instantarticles-wp-plugin' => array(
                        'class' => 'LoggerAppenderConsole',
                        'threshold' => 'INFO',
                        'layout' => array(
                            'class' => 'LoggerLayoutSimple',
                        ),
                    ),
                ),
            )
        );

        return $this;
    }
    /**
     * Load Rules
     *
     * @return $this
     */
    private function loadRules(){
        // Handle custom transformation rules
        if($this->configuration->customTransformerRules){
            $transformerRules = $this->configuration->customTransformerRules;
        }else{
            if(File::exists($this->getData()->assetsPath()."/rules/general.json")){
                $transformerRules = file_get_contents($this->getData()->assetsPath()."/rules/general.json", true);
            }else{
                throw new Exception("No rules find found");
            }

        }

        // Load rules
        $this->transformer = new Transformer();
        $this->transformer->loadRules($transformerRules);

        return $this;
    }

    /**
     * Create Header
     *
     * @return $this
     */
    private function setHeader(){
        // Get time zone configured in CMS. Default to UTC if no time zone configured.
        $timeZone = settings( 'timezone' ) ? new \DateTimeZone( settings( 'timezone' ) ) : new \DateTimeZone( 'Europe/London' );

        $this->header = Header::create()
            ->withPublishTime(
                Time::create( Time::PUBLISHED )->withDatetime( new \DateTime( $this->post->created_at, $timeZone ) )
            )
            ->withModifyTime(
                Time::create( Time::MODIFIED )->withDatetime( new \DateTime( $this->post->updated_at, $timeZone ) )
            );
        return $this;
    }

    /**
     * Set title
     *
     * @return $this
     */
    private function setTitle(){
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML( '<?xml encoding="utf-8" ?><h1>' . $this->post->title . '</h1>' );
        libxml_use_internal_errors(false);

        $this->transformer->transform( $this->header, $document );

        return $this;
    }

    /**
     * Create Instant Article instance
     * @return $this
     */
    public function initInstantArticle(){
        $this->instantArticle = InstantArticle::create()
            ->withCanonicalUrl($this->post->href)
            ->withHeader($this->header)
            ->addMetaProperty( 'op:generator:application', 'facebook-instant-articles-wp' )
            ->addMetaProperty( 'op:generator:application:version', $this->getData()->config()->version);

        return $this;
    }

    /**
     * Set author
     *
     * @return $this
     */
    public function setAuthors(){
        $user = $this->post->cachedUser();
        if($user){
            $authorObj = Author::create();

            $authorObj->withName($user->firstName." ".$user->lastName);

            if ($user->about) {
                $authorObj->withDescription($user->about);
            }
            $authorObj->withURL($user->href );

            $this->header->addAuthor( $authorObj );
        }
        return $this;
    }
    /**
     * Apply appearance settings for an InstantArticle.
     *
     * @since 3.3
     */
    private function setStyle() {
        if ($this->configuration->style) {
            $this->instantArticle->withStyle($this->configuration->style);
        } else {
            $this->instantArticle->withStyle( 'default' );
        }

        if ($this->configuration->copyright) {
            $footer = Footer::create();
            $this->transformer->transformString(
                $footer,
                '<small>' . $this->configuration->copyright . '</small>'
            );
            $this->instantArticle->withFooter( $footer );
        }

        if ($this->configuration->rtl) {
            $this->instantArticle->enableRTL();
        }
        return $this;
    }

    /**
     * Set likes on media
     *
     * @return $this
     */
    private function setLikesOnMedia(){
        Image::setDefaultLikeEnabled( $this->configuration->showLikes );
        Video::setDefaultLikeEnabled( $this->configuration->showLikes );
        return $this;
    }

    /**
     * Set likes on media
     *
     * @return $this
     */
    private function setCommentsOnMedia(){
        Image::setDefaultCommentEnabled( $this->configuration->showComments );
        Video::setDefaultCommentEnabled( $this->configuration->showComments );
        return $this;
    }

    /**
     * Clean post content's html tags
     *
     * @param mixed $content
     * @return mixed
     */
    private function cleanHtmlTags($content){
        // Remove empty paragraphs
        $content = preg_replace("/<p[^>]*><\\/p[^>]*>/", '', $content);
        $content = preg_replace('~\\s?<p>(\\s|<br\\/])+</p>\\s?~', '', $content);
        $content = str_replace(['<p> <br> </p>','<p><br></p>'], '', $content);
        return $content;
    }

    /**
     * Set post content
     *
     * @return $this
     */
    private function setContent(){
        $content = $this->cleanHtmlTags($this->post->content());
        $this->transformer->transformString( $this->instantArticle, $content);
        return $this;
    }

    /**
     * Add all ad code(s) to the Header of an InstantArticle.
     *
     * @return $this
     */
    public function setAds() {
        $width = 300;
        $height = 250;

        $ad = Ad::create()
            ->enableDefaultForReuse()
            ->withWidth( $width )
            ->withHeight( $height );

        switch ( $this->configuration->adType ) {

            case 'audience_network':
                if (!empty( $this->configuration->adPlaceentIDs)) {
                    $ad->withSource('https://www.facebook.com/adnw_request?placement='.$this->configuration->adPlaceentIDs.'&adtype='.'banner' . $width . 'x' . $height);
                    $this->header->addAd( $ad );
                }
                break;

            case 'iframe_url':
                if (!empty($this->configuration->adIframeURL)){
                    $ad->withSource(
                        $this->configuration->adIframeURL
                    );

                    $this->header->addAd( $ad );
                }
                break;

            case 'embed_code':
                if (!empty($this->configuration->adCustomCode)){
                    $document = new DOMDocument();
                    $fragment = $document->createDocumentFragment();
                    $validHTML = @$fragment->appendXML($this->configuration->adCustomCode);

                    if ($validHTML) {
                        $ad->withHTML($fragment);
                        $this->header->addAd( $ad );
                    }
                }
                break;
        }

        $this->instantArticle->enableAutomaticAdPlacement();

        return $this;
    }

    /**
     * Set post cover (Image or Video)
     *
     * @return $this
     */
    private function setCover(){
        // Handle Image
        $featuredImage = $this->post->featuredImage;
        if($featuredImage){
            $imageObject = Image::create()
                ->withURL(url($featuredImage->url))
                ->withCaption(
                    Caption::create()->appendText($featuredImage->description)
                );
            $this->header->withCover( $imageObject );
        }

        return $this;
    }
    /**
     * Set meta code (Analytics or other meta code)
     *
     * @return $this
     */
    private function setMetaCode(){
        if ($this->configuration->metaCode) {
            $document = new \DOMDocument();
            $fragment = $document->createDocumentFragment();
            $isValidHTML = @$fragment->appendXML($this->configuration->metaCode);

            if ($isValidHTML) {
                $this->instantArticle
                    ->addChild(
                        Analytics::create()
                            ->withHTML($fragment)
                    );
            }
        }
        return $this;
    }

    /**
     * Publish instant article
     * @param boolean $isPublished
     *
     * @return boolean
     */
    private function publish($isPublished){
        // On development mode
        if($this->configuration->developmentMode){
            $isPublished = false;
        }

        // Instantiate an API client
        $client = Client::create(
            $this->configuration->appID,
            $this->configuration->appSecret,
            $this->configuration->accessToken,
            $this->configuration->pageID
        );

        try {
            $client->importArticle($this->instantArticle, ($isPublished ? true : false));
            return true;
        } catch (Exception $e) {
            $client->importArticle($this->instantArticle, false);
            $this->publishingError = $e->getMessage();
        }
        return false;
    }

    /**
     * @param Command $command
     * @return bool
     */
    public function install(Command $command){

        if(!Schema::hasTable('accio_instant_articles')) {
            Schema::create('accio_instant_articles', function ($table)  {
                $table->increments("instantArticleID");
                $table->integer("appID")->unsigned();
                $table->integer("pageID")->unsigned();
                $table->string("appSecret", 255);
                $table->string("accessToken", 255);
                $table->string("style", 25);
                $table->text("customTransformerRules");
                $table->string("copyright", 255);
                $table->text("metaCode");
                $table->tinyInteger("rtl");
                $table->tinyInteger("developmentMode");
                $table->tinyInteger("showComments");
                $table->tinyInteger("showLikes");
                $table->tinyInteger("showRelatedPosts");
                $table->string("adType", 45);
                $table->text("adPlacementIDs");
                $table->string("adIframeURL", 255);
                $table->text("adCustomCode");
                $table->timestamps();
            });
        }

        return true;
    }


}