<template>
    <div v-if="tab=='main'">
        <qiwi-wallet-general-settings></qiwi-wallet-general-settings>
    </div>
    <div v-else-if="tab=='security'">
        <qiwi-wallet-security-settings></qiwi-wallet-security-settings>
    </div>
    <div v-else-if="tab=='identification'">
        <qiwi-wallet-identification></qiwi-wallet-identification>
    </div>
</template>
<script>
    export default{
        data(){
            return {
//                tab: "security",
//                tab: "main",
                tab: "identification",
            }
        },
        mounted(){
            this.$nextTick(() => {
                $('.tooltip').removeClass('in');
            });
        }

    }
</script>
<style>
    .full-width {
        width: 100%
    }

    .marginless {
        margin: 0 auto;
    }

    .paddingless {
        padding-left: 0;
        padding-right: 0;
    }
</style>