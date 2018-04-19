<template>
    <div class="panel-wrapper">
        <div class="form-group">
            <label class="control-label col-md-4 col-sm-4 col-xs-12">Publish this post as instant article</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div id="isDefault" class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default" :class="{active: pluginData.importInstantArticle}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="pluginData.importInstantArticle = true">
                        <input type="radio" name="default" :value="true">&nbsp; {{trans.__true}} &nbsp;
                    </label>
                    <label class="btn btn-primary" :class="{active: pluginData.importInstantArticle}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @click="pluginData.importInstantArticle = false">
                        <input type="radio" name="default" :value="false"> {{trans.__false}}
                    </label>
                    <div class="alert" v-if="StoreResponse.errors.importInstantArticle" v-for="error in StoreResponse.errors.importInstantArticle">{{ error }}</div>
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
        props: ['pluginData'],
        created(){
            this.pluginData.importInstantArticle = false;
        }
    }
</script>