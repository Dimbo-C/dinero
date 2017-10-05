<template>
    <div>
        <page-header icon="fa-money" title="Ваучеры">
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

        <loading :show="processed"></loading>

        <div v-if="!processed" class="container-fluid">
            <div class="row">

                <div v-if="!responseObtained" class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">Активация ваучера Qiwi</div>
                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Код ваучера</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"
                                               placeholder="Например: L5MQLT8PH8339M715NE6K1PKD"
                                               v-model="voucherCode">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-4 col-sm-8">
                                    <button class="btn btn-primary" @click="activateVoucher">Активировать ваучер
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">Создание ваучера Qiwi</div>
                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Сумма</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"
                                               placeholder="Например: 200"
                                               v-model="voucherSum">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-4 col-sm-8">
                                    <button class="btn btn-primary"
                                            @click="createVoucher">Создать ваучер
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="responseObtained" class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">Информация по ваучеру {{voucherCode}}</div>
                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Код ваучера</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"
                                               placeholder="Например: L5MQLT8PH8339M715NE6K1PKD"
                                               v-model="voucherCode">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-4 col-sm-8">
                                    <button class="btn btn-primary" @click="activateVoucher">Активировать ваучер
                                    </button>
                                </div>
                            </div>
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
                login: "",
                voucherCode: "",
                voucherSum: 0,
                processed: false,
                responseObtained: false,
                code: ""
            };
        },
        mounted(){
            this.login = this.$route.params.wallet;
        },
        watch: {},
        methods: {
            activateVoucher(){
                this.processed = true;
                let data = {login: this.login, code: this.voucherCode};
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/activate-voucher`, new Form(data))
                    .then((response) => {
                        Vue.ls.set('response_egg_activation', response);
                        this.responseObtained = true;

                        this.processed = false;
                    });
            },
            createVoucher(){

            },
        },

        computed: {
            walletTypeDescription()            {
                return this.walletTypes.find(t => t.value === this.form.type).description;
            }
        }
    }
</script>
