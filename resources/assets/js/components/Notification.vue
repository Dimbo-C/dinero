<template>
    <transition name="fade">
        <div class="alert alert-dismissable notification" :class="alertClass" role="alert" v-show="isShown">
            <button type="button" class="close" @click="hideNotification()">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 v-html="message"></h4>
        </div>
    </transition>
</template>

<script>
    export default {
        data() {
            return {
                isShown: false,
                alertClass: 'alert-warning',
                message: '',
            }
        },

        created() {
            const self = this;

            Bus.$on('showNotification', function (alertClass, message) {
                self.showNotification(alertClass, message);
            });
        },

        methods: {
            showNotification(alertClass, message) {
                this.isShown = true;
                this.alertClass = `alert-${alertClass}`;
                this.message = message;

                setTimeout(() => {
                    this.hideNotification()
                }, 20000);
            },

            hideNotification() {
                this.isShown = false;

                setTimeout(() => {
                    this.message = '';
                }, 1000);

            }
        }
    }
</script>