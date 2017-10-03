<template>
    <div>
        <page-header icon="fa-money" title="Добавление кошелька">
            <li>
                <a class="disabled">Финансы</a>
            </li>
            <router-link tag="li" to="/finance/qiwi">
                <a>Qiwi Visa Wallet</a>
            </router-link>
            <router-link tag="li" to="/finance/qiwi/dashboard">
                <a>Панель управления</a>
            </router-link>
        </page-header>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3 class="body-header">Подтверждение массовой операции</h3>
                            <template v-if="!success">
                                <div class="body-content">
                                    <div class="alert-notification">
                                        <b>Вы уверены что хотите произвести операцию {{ operationTitle }} ?</b>
                                    </div>
                                    <div class="list-area">
                                        <b>Вы выполните операцию над следующими кошельками:</b>
                                        <ul type="none">
                                            <li v-for="w in wallets" v-if="w.is_active || isInactive">
                                                <div class="row">
                                                    <div class="col-sm-2">{{ w.name }}</div>
                                                    <div class="col-sm-2">{{ w.login }}</div>
                                                </div>

                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12 col-lg-offset-2 col-lg-4">
                                                <button class="btn btn-success proceed"
                                                        @click="proceed">
                                                    Выполнить
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-lg-4">
                                                <button class="btn btn-default back"
                                                        @click="back">
                                                    Назад
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                success: false,
                confirm: true,
                login: this.$route.params.wallet,
                wallets: [],

                operationTitle: ""
            }
        },
        mounted(){
            this.wallets = Vue.ls.get('wallets');
            this.action = Vue.ls.get('action');
            let actions = Vue.ls.get('actions');

            this.operationTitle = actions[this.action];
        },
        methods: {
            proceed() {
                let postData = {action: this.action, wallets: this.wallets};
                axios.post('/api/qiwi-wallets/mass-action', postData)
                    .then((response) => {
                        console.log(response);
                        this.back();
                    });
            },

            back(){
                this.$router.push({path: `/finance/qiwi/dashboard`});
            }
        },

    }
</script>

<style scoped>
    .body-header {
        padding: 15px 0;
        margin-top: 0;
        font-weight: bold;
        font-size: 16px;
        border-bottom: 1px solid;
    }

    .proceed, .back {
        width: 100%;
        display: block;
    }

    .body-content {
        padding: 15px;
    }

    .alert-notification {
        padding: 12px;
        margin-bottom: 15px;
        margin-left: 0;
        /*color: #8a6d3b;*/
        font-size: 14px;
        background-color: #fcf8e3;
        border-color: #d6e9c6;
        border-radius: 3px;
    }

    .success-notification {
        padding: 12px;
        margin-bottom: 15px;
        color: #3c763d;
        font-size: 14px;
        background-color: #dff0d8;
        border-color: #d6e9c6;
        border-radius: 3px;
    }

    .list-area {
        margin-left: 12px;
    }

    .body-content {

        margin-left: auto;
        margin-right: auto;
    }
</style>
