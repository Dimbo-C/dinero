<template>
    <div class="jumbotron">
        <div class="container-fluid">
            <div class="media">
                <div class="media-left">
                    <span class="fa-stack fa-2x">
                        <i class="fa fa-stack-1x fa-inverse" :class="icon"></i>
                    </span>
                </div>
                <div class="media-body">
                    <ol class="breadcrumb">
                        <li>
                            <router-link to="/dashboard"><i class="fa fa-home"></i></router-link>
                        </li>

                        <slot></slot>

                        <li>
                            <span class="active" v-text="title"></span>
                        </li>
                    </ol>
                    <h3 class="media-heading" v-text="title"></h3>
                </div>
            </div>
        </div>

        <notification></notification>
    </div>
</template>

<script>
  import Notification from './Notification.vue'

  export default {
    components: {
      Notification
    },
    props: ['icon', 'title'],
  };
</script>