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
                            <div class="wallet-info">
                                <div class="alert"
                                     :class="notificationClass"
                                     v-html="responseText">
                                </div>
                                <p>
                                    Баланс: {{updatedBalance}}
                                </p>
                                <p>Вы можете перейти <a href="#" v-on:click.stop="back">назад</a>
                                    или к
                                    <router-link to="/finance/qiwi/dashboard"><a>списку</a></router-link>
                                    кошельков.
                                </p>
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
                responseText: "",
                notificationClass: "alert-danger",
                code: "",
                updatedBalance: "(Загружается... )"
            };
        },
        mounted(){
            this.login = this.$route.params.wallet;
        },
        watch: {},
        methods: {
            back(){
                this.responseObtained = false;
            },
            activateVoucher(){
                this.processed = true;
                const form = new Form({login: this.login, code: this.voucherCode});
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/activate-voucher`, form)
                    .then((response) => {
                        this.responseObtained = true;
                        this.notificationClass = response.status == 200 ? "alert-success" : "alert-danger";
                        this.responseText = response.resultText;
                        this.processed = false;
                        this.updateWallet(this.login);
                    });
            },
            createVoucher(){
                this.processed = true;
                const form = new Form({login: this.login, amount: this.voucherSum});
                console.log(form);
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/create-voucher`, form)
                    .then((response) => {
                        console.log(response);
                        this.responseObtained = true;
                        this.notificationClass = response.status == 200 ? "alert-success" : "alert-danger";
                        this.responseText = response.resultText;
                        this.processed = false;
                        this.updateWallet(this.login);
                    });
            },

            updateWallet(login) {
                let auth = {"login": login};
                Dinero.post('/api/qiwi-wallets/update-balance', new Form(auth))
                    .then((balance) => {
                        this.updatedBalance = balance;
                    })
            },
        },

        computed: {
            walletTypeDescription()            {
                return this.walletTypes.find(t => t.value === this.form.type).description;
            }
        }
    }
</script>