<template>
    <div v-if="tab=='main'">
        <qiwi-wallet-general-settings></qiwi-wallet-general-settings>
    </div>
    <div v-else-if="tab=='security'">
        <qiwi-wallet-security-settings></qiwi-wallet-security-settings>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                tab: "security"
            }
        }
    }
</script>