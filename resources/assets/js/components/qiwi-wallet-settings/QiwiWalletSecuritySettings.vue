<template>
    <div>
        <page-header icon="fa-money" title="Вывод средств">
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
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-12 col-md-10">
                                    Настройки безопасности кошелька Qiwi ({{ login}})
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <button class="btn btn-primary"
                                            @click="showGeneralSettings">
                                        Настройки
                                    </button>
                                </div>
                            </div>
                        </div>
                        <loading :show="!isLoaded"></loading>
                        <div v-if="isLoaded" class="panel-body">
                            <div class="form-horizontal">
                                <div v-if="!smsConfirmationBlock" class="form-group">
                                    <div class="col-sm-offset-1 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="smsConfirmation"
                                                       @click="smsConfirmationCheckbox"
                                                       checked>
                                                Подтверждение платежей по СМС
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="smsConfirmationBlock">
                                    <label for="" class="col-sm-12 col-md-3 control-label">Код из смс</label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text"
                                               class="form-control"
                                               v-model="smsConfirmCode"
                                               placeholder="123456">
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <button class="btn btn-primary"
                                                @click="confirmSms">
                                            Подтвердить
                                        </button>

                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="emailBinding"
                                                       checked>
                                                Привязка email
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="useToken"
                                                       checked
                                                       @click="tokenCheckbox">
                                                Защита операций с помощью токена
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="usePinCode"
                                                       checked
                                                       @click="pinCodeCheckbox">
                                                Пин-код
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="smsPayments"
                                                       checked
                                                       @click="smsPaymentCheckbox">
                                                Смс-платежи
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="callConfirm"
                                                       checked>
                                                Кол-конфирм
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <!--<div class="form-group">-->
                                <!--<div class="col-sm-offset-4 col-sm-8">-->
                                <!--<button class="btn btn-primary"-->
                                <!--@click="saveSettings">-->
                                <!--Сохранить-->
                                <!--</button>-->
                                <!--</div>-->
                                <!--</div>-->

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
                smsConfirmation: false,
                smsConfirmationBlock: false,
                smsConfirmCode: "",
                smsToken: "",

                emailBinding: false,
                useToken: false,
                usePinCode: false,
                smsPayments: false,
                callConfirm: false,
                login: this.$route.params.wallet,
                isLoaded: false,
            }
        },
        mounted() {
            this.fetchSettings();
        },
        methods: {
            smsConfirmationCheckbox() {
                console.log(this.smsConfirmation);
                const check = this.smsConfirmation;

                const data = {
                    'login': this.login,
                    'action': "SMS_CONFIRMATION",
                    'options': {
                        'value': check,
                        'token': this.smsToken
                    }
                };

                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            this.smsToken = data.token;
                            if (!check) {
                                this.smsConfirmationBlock = true;
                            }
                        });
            },

            pinCodeCheckbox(){
                const func = (data) => {
                    console.log(data);
                };
                this.switcherBase(this.usePinCode, "PIN", func);
            },

            tokenCheckbox(){
                const func = (data) => {
                    console.log(data);
                };
                this.switcherBase(this.useToken, "TOKEN", func);
            },

            smsPaymentCheckbox(){
                const func = (data) => {
                    console.log(data);
                };
                this.switcherBase(this.smsPayments, "SMS_PAYMENT", func);
            },

            switcherBase(field, action, func){
                const data = {
                    'login': this.login,
                    'action': action,
                    'options': {
                        'value': field,
                    }
                };

                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            func(data);
                        });
            },

            confirmSms(){
                const data = {
                    'login': this.login,
                    'action': "SMS_CONFIRMATION",
                    'options': {
                        'code': this.smsConfirmCode,
                        'token': this.smsToken
                    }
                };
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            this.smsConfirmation = false;
                            this.smsConfirmationBlock = false;
                        });
            },
            fetchSettings() {
                axios.get(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, {})
                        .then((response) => {
                            console.log(response);
                            const data = response.data;
                            this.callConfirm = data.CALL_CONFIRMATION;
                            this.emailBinding = data.EMAIL;
                            this.usePinCode = data.PIN;
                            this.smsConfirmation = data.SMS_CONFIRMATION;
                            this.smsPayments = data.SMS_PAYMENT;
                            this.useToken = data.TOKEN;

                            this.isLoaded = true;
                        });
            },
            showGeneralSettings() {
                this.$parent.tab = 'main';
            }
        }
    }
</script>