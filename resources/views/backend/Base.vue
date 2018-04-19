<template>
    <div class="right_col" role="main">
        <div class="componentsWs">
            <!-- TITLE -->
            <div class="page-title">
                <div class="title_left">
                    <h3 class="pull-left">Facebook instant article</h3>
                </div>
            </div>
            <!-- TITLE END -->
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <spinner v-if="spinner" :width="'30px'" :height="'30px'" :border="'5px'"></spinner>

                    <div v-if="!spinner">
                        <div class="x_panel" v-if="!appLinked" >
                            <div class="x_title">
                                <h2>Login</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br />
                                <form  class="form-horizontal form-label-left" enctype="multipart/form-data">
                                    <div class="form-group" id="form-group-appID">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">App ID</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control" id="name" v-model="app_id">
                                            <div class="alert" v-if="StoreResponse.errors.appID" v-for="error in StoreResponse.errors.appID">{{ error }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group" id="form-group-appSecret">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">App secret</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control" v-model="app_secret">
                                            <div class="alert" v-if="StoreResponse.errors.appSecret" v-for="error in StoreResponse.errors.appSecret">{{ error }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group" id="form-group-pageID">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Page ID</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control" v-model="page_id">
                                            <div class="alert" v-if="StoreResponse.errors.page_id" v-for="error in StoreResponse.errors.page_id">{{ error }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-3 col-sm-3 col-xs-12"></div>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <button type="button" class="btn btn-primary" @click="login">Login</button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-3 col-sm-3 col-xs-12"></div>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-3 col-sm-3 col-xs-12"></div>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            OR <br />
                                            <button type="button" class="btn btn-small btn-info" @click="backToConfiguration()" >{{trans.__backToConfiguration}}</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                        <div class="x_panel" v-else>
                            <div class="x_title">
                                <h2>Configuration</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <form  class="form-horizontal form-label-left"  enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <h2><strong>Appearance</strong></h2>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Style name</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control" v-model="configForm.style">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Copyright note</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control" v-model="configForm.copyright">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Enable RTL (Right To Left)</label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div id="isDefault" class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-default" :class="{active: configForm.rtl}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.rtl = true">
                                                    <input type="radio" name="default" :value="true" v-model="configForm.rtl"> &nbsp; {{trans.__true}} &nbsp;
                                                </label>
                                                <label class="btn btn-primary" :class="{active: configForm.rtl}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.rtl = false">
                                                    <input type="radio" name="default" :value="false" v-model="configForm.rtl"> {{trans.__false}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <h2><strong>Ads</strong></h2>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Ad type</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <select class="form-control" v-model="configForm.adType" @change="showAdFields(configForm.adType)">
                                                <option selected value="">None</option>
                                                <option v-for="(option, key, index) in adTypes" :value="key">{{option}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group" v-if="showAdAudienceNetwork">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Audience Network Placement ID</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input  type="text" class="form-control" v-model="configForm.adPlacementIDs">
                                        </div>
                                    </div>

                                    <div class="form-group" v-if="showAdIframeURL">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Source URL</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control" v-model="configForm.adIframeURL">
                                        </div>
                                    </div>

                                    <div class="form-group" v-if="showAdCustomCode">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Custom Embed Code</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <textarea class="form-control" v-model="configForm.adCustomCode"> </textarea>
                                        </div>
                                    </div>

                                    <div class="form-group" >
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <h2><strong>Code</strong></h2>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Meta Code</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <p>
                                                Add code for any other analytics services you wish to use.
                                            </p>
                                            <textarea class="form-control" v-model="configForm.metaCode"> </textarea>
                                            <p>Note: You do not need to include any html tags. The plugin will automatically include them in the article markup.</p>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Custom Transformation rules</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <textarea class="form-control" v-model="configForm.customTransformerRules"> </textarea>
                                            <p>
                                                If set, default transformation rules will be ignored
                                            </p>
                                        </div>
                                    </div>


                                    <div class="form-group" id="form-group-appConfiguration">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <h2><strong>Publishing settings</strong></h2>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Enable Development Mode</label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div  class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-default" :class="{active: configForm.developmentMode}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.developmentMode = true">
                                                    <input type="radio" name="default" :value="true" v-model="configForm.developmentMode"> &nbsp; {{trans.__true}} &nbsp;
                                                </label>
                                                <label class="btn btn-primary" :class="{active: configForm.rtl}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.developmentMode = false">
                                                    <input type="radio" name="default" :value="false" v-model="configForm.developmentMode"> {{trans.__false}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Show Comments</label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-default" :class="{active: configForm.showComments}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.showComments = true">
                                                    <input type="radio" name="default" :value="true" v-model="configForm.showComments"> &nbsp; {{trans.__true}} &nbsp;
                                                </label>
                                                <label class="btn btn-primary" :class="{active: configForm.rtl}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.showComments = false">
                                                    <input type="radio" name="default" :value="false" v-model="configForm.showComments"> {{trans.__false}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Show Likes</label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div  class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-default" :class="{active: configForm.showLikes}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.showLikes = true">
                                                    <input type="radio" name="default" :value="true" v-model="configForm.showLikes"> &nbsp; {{trans.__true}} &nbsp;
                                                </label>
                                                <label class="btn btn-primary" :class="{active: configForm.rtl}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.showLikes = false">
                                                    <input type="radio" name="default" :value="false" v-model="configForm.showLikes"> {{trans.__false}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Show Related Posts</label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-default" :class="{active: configForm.showRelatedPosts}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.showRelatedPosts = true">
                                                    <input type="radio" name="default" :value="true" v-model="configForm.showRelatedPosts"> &nbsp; {{trans.__true}} &nbsp;
                                                </label>
                                                <label class="btn btn-primary" :class="{active: configForm.rtl}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="configForm.showRelatedPosts = false">
                                                    <input type="radio" name="default" :value="false" v-model="configForm.showRelatedPosts"> {{trans.__false}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-3 col-sm-3 col-xs-12"></div>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <button type="button" class="btn btn-primary" id="globalSaveBtn" @click="saveConfiguration">Save changes</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-3 col-sm-3 col-xs-12"></div>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                        </div>
                                    </div>

                                    <div class="form-group" id="form-group-submit">
                                        <div class="col-md-3 col-sm-3 col-xs-12"></div>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            OR <br />
                                            <button type="button" class="btn-small btn-info" @click="changeAppInfo()" >{{trans.__changeAppinfo}}</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</template>
<script>
    import { globalComputed } from '../../../../../../vendor/acciocms/core/src/resources/views/mixins/globalComputed';
    import { globalMethods } from '../../../../../../vendor/acciocms/core/src/resources/views/mixins/globalMethods';
    import { globalData } from '../../../../../../vendor/acciocms/core/src/resources/views/mixins/globalData';
    import { trans } from '../../../../../../vendor/acciocms/core/src/resources/views/mixins/trans';

    export default{
        mixins: [globalComputed, globalMethods, globalData, trans],
        data(){
            return{
                app_id: '245103745968585',
                app_secret: '4b389692dc03c170bf768b12b8160268',
                page_id: '489353424605405',
                appLinked: false,
                reconfigure: false,
                pluginURL: this.$store.getters.get_base_path+'/'+this.$route.params.adminPrefix+'/'+this.$route.params.lang+'/plugins/accio/instant-articles',
                adTypes: {
                    audience_network: "Facebook Audience Network",
                    iframe_url: "Custom Iframe URL",
                    embed_code: "Custom Embed Code"
                },
                showAdAudienceNetwork: false,
                showAdIframeURL: false,
                showAdCustomCode: false,
                configForm: {
                    style: 'default',
                    customTransformerRules: '',
                    copyright: 'All rights reserved',
                    metaCode: '',
                    rtl: false,
                    developmentMode: false,
                    showComments: false,
                    showLikes: false,
                    showRelatedPosts: false,
                    adType: 'audience_network',
                    adPlacementIDs: '',
                    adIframeURL: '',
                    adCustomCode: ''
                }
            }
        },
        mounted(){
            var global = this;

            // translations
            this.trans.__changeAppinfo = this.__('Accio.InstantArticles::base.change_app_info');
            this.trans.__backToConfiguration = this.__('Accio.InstantArticles::base.back_to_configuration');

            // init fb sdk
            // Execute FB SDK
            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            // Check if FB App is connected
            this.$store.commit('setSpinner', true);
            this.$http.get(this.pluginURL+'/get-details', "test")
                .then((resp) => {
                    global.$store.commit('setSpinner', false);

                    if(resp.body.code == 200){
                        // Fill config form
                        this.configForm.style = resp.body.data.style;
                        this.configForm.customTransformerRules = resp.body.data.customTransformerRules;
                        this.configForm.copyright = resp.body.data.copyright;
                        this.configForm.metaCode = resp.body.data.metaCode;
                        this.configForm.rtl = resp.body.data.rtl;
                        this.configForm.developmentMode = resp.body.data.developmentMode;
                        this.configForm.showComments = resp.body.data.showComments;
                        this.configForm.showLikes = resp.body.data.showLikes;
                        this.configForm.showRelatedPosts = resp.body.data.showRelatedPosts;
                        this.configForm.adType = resp.body.data.adType;
                        this.configForm.adPlacementIDs = resp.body.data.adPlacementIDs;
                        this.configForm.adIframeURL = resp.body.data.adIframeURL;
                        this.configForm.adCustomCode = resp.body.data.adCustomCode;

                        // Show ad fields
                        this.showAdFields(this.configForm.adType);

                        // Show config form
                        global.appLinked = true;

                    }
                });
        },
        methods:{
            login(){

                var global = this;
                var request = {
                    app_id: this.app_id,
                    app_secret: this.app_secret,
                    page_id: this.page_id,
                    response: '',
                };

                FB.init({
                    appId: global.app_id,
                    cookie: true, // This is important, it's not enabled by default
                    version: 'v2.2'
                });

                // Show Loading
                this.$store.dispatch('openLoading');

                // FB login
                FB.login(function(response) {
                    request.response = response;

                    if(response.authResponse) {
                        global.$http.post(global.pluginURL+'/login-callback', request)
                            .then((resp) => {
                                global.$store.dispatch('closeLoading');
                                global.appLinked = true;
                                global.noty("success",'bottomLeft',resp.body.message,3000);
                            });
                    }else{
                        global.$store.dispatch('closeLoading');
                        global.noty("error",'bottomLeft','Instant Articles not connected');
                    }
                },{ scope: 'email,pages_show_list,pages_manage_instant_articles'});
                return false;

            },
            saveConfiguration(){
                var global = this;
                var request = {
                    style: this.configForm.style,
                    customTransformerRules: this.configForm.customTransformerRules,
                    copyright: this.configForm.copyright,
                    metaCode: this.configForm.metaCode,
                    rtl: this.configForm.rtl,
                    developmentMode: this.configForm.developmentMode,
                    showComments: this.configForm.showComments,
                    showLikes: this.configForm.showLikes,
                    showRelatedPosts: this.configForm.showRelatedPosts,
                    adType: this.configForm.adType,
                    adPlacementIDs: this.configForm.adPlacementIDs,
                    adIframeURL: this.configForm.adIframeURL,
                    adCustomCode: this.configForm.adCustomCode
                };
 
                // Show Loading
                this.$store.dispatch('openLoading');

                this.$http.post(global.pluginURL+'/save-configuration', request)
                .then((resp) => {
                    global.$store.dispatch('closeLoading');

                    if(resp.body.code == 200){
                        this.appLinked = true;
                        this.noty("success",'bottomLeft',resp.body.message,3000);
                    }else{
                        this.noty("error",'bottomLeft',resp.body.message,3000);
                    }
                });

                return false;
            },

            hideAdCodes(){
                this.showAdAudienceNetwork = false;
                this.showAdIframeURL = false;
                this.showAdCustomCode = false;
            },

            showAdFields(adType){
                this.hideAdCodes();

                switch (adType){
                    case "audience_network":
                        this.showAdAudienceNetwork = true;
                        break;
                    case "custom_iframe_url":
                        this.showAdIframeURL = true;
                        break;
                    case "custom_embed_code":
                        this.showAdCustomCode = true;
                        break;
                }
            },
            changeAppInfo(){
                this.appLinked = false;
                this.reconfigure = true;
            },
            backToConfiguration(){
                this.appLinked = true;
                this.reconfigure = false;
            }
        }
    }
</script>