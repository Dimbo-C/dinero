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
                                <div class="col-sm-12 col-md-8">
                                    Настройки безопасности кошелька Qiwi ({{ login}})
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <button class="btn btn-primary full-width"
                                            @click="showSetting('main')">Настройки
                                    </button>
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <button class="btn btn-primary full-width marginless paddingless"
                                            @click="showSetting('identification')">
                                        Идентификация
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
                                                       checked
                                                       @click="emailCheckbox">
                                                Привязка email
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="emailInputBlock">
                                    <label for="" class="col-sm-12 col-md-3 control-label">email для привязки</label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text"
                                               class="form-control"
                                               v-model="email"
                                               placeholder="email@org.com">
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <button class="btn btn-primary"
                                                @click="emailSendConfirm">
                                            Подтвердить
                                        </button>

                                    </div>
                                </div>

                                <div class="form-group" v-if="emailSmsBlock">
                                    <label for="" class="col-sm-12 col-md-3 control-label">Код из смс</label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text"
                                               class="form-control"
                                               v-model="emailSmsCode"
                                               placeholder="123456">
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <button class="btn btn-primary"
                                                @click="emailSmsConfirm">
                                            Подтвердить
                                        </button>

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

                                <div class="form-group" v-if="!callConfirmSmsBlock">
                                    <div class="col-sm-offset-1 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="callConfirm.checkbox"
                                                       @click="callConfirmCheckbox"
                                                       checked>
                                                Кол-конфирм
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="callConfirmSmsBlock">
                                    <label for="" class="col-sm-12 col-md-3 control-label">Код из смс</label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text"
                                               class="form-control"
                                               v-model="callConfirm.smsCode"
                                               placeholder="123456">
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <button class="btn btn-primary"
                                                @click="emailSmsConfirm">
                                            Подтвердить
                                        </button>

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
                emailInputBlock: false,
                emailSmsBlock: false,
                emailSmsCode: "",
                emailSmsToken: "",
                email: "",

                useToken: false,
                usePinCode: false,
                smsPayments: false,

                callConfirm: {
                    checkbox: false,
                    smsBlock: false,
                    smsCode: "",
                },
//                callConfirm: false,
//                callConfirmSmsBlock: false,
//                callConfirmSmsCode: "",

                login: this.$route.params.wallet,
                isLoaded: false,
            }
        },
        mounted() {
            this.fetchSettings();
        },
        methods: {

            // handler for un-checking sms confirmation checkbox
            smsConfirmationCheckbox() {
                console.log(this.smsConfirmation);
                const data = {
                    'login': this.login,
                    'action': "SMS_CONFIRMATION",
                    'options': {
                        'value': this.smsConfirmation,
                        'token': this.smsToken
                    }
                };

                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            this.smsToken = data.token;
                            if (!this.smsConfirmation) {
                                this.smsConfirmationBlock = true;
                            }
                        });
            },

            // handler for email checkbox switching
            emailCheckbox(){
                if (this.emailBinding) {
                    this.emailInputBlock = true;
                } else {
                    this.emailFetchUnbindToken();
                }
            },

            // sent confirmation letter to email in input block
            emailSendConfirm(){
                const data = {
                    'login': this.login,
                    'action': "EMAIL",
                    'options': {
                        'email': this.email,
                    }
                };
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            if (data.success) {
                                Bus.$emit('showNotification', "success", "Письмо для подтверждения отправлено на почту");
                                this.emailInputBlock = false;
                            } else {
                                Bus.$emit('showNotification', "danger", "Ошибка, проверьте введенный email");
                            }
                        });
            },

            // get token for unbinding email from wallet (sms is sent to mobile)
            emailFetchUnbindToken(){
                this.emailSmsBlock = true;
                const data = {
                    'login': this.login,
                    'action': "EMAIL",
                    'options': {}
                };
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            this.emailSmsToken = data.token;

                        });
            },

            // submit code from sms for email
            emailSmsConfirm(){
                const data = {
                    'login': this.login,
                    'action': "EMAIL",
                    'options': {
                        'code': this.emailSmsCode,
                        'token': this.emailSmsToken
                    }
                };
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            if (data.success) {
                                Bus.$emit('showNotification', "success", `Почта ${this.email} отвязана от этого кошелька`);
                            } else {
                                Bus.$emit('showNotification', "danger", "Ошибка, введенный код неверен");
                                this.emailBinding = true;
                            }
                            this.emailSmsBlock = false;
                        });
            },

            // switchers for items that require no confirmation
            pinCodeCheckbox(){
                this.switcherRoutine("usePinCode", "PIN", "Пин код");
            },

            tokenCheckbox(){
                this.switcherRoutine("useToken", "TOKEN", "Защита операций с помощью токена");
            },

            smsPaymentCheckbox(){
                this.switcherRoutine("smsPayments", "SMS_PAYMENT", "Смс-платежи");
            },

            switcherRoutine(fieldName, action, fieldNameText){
                console.log(this[fieldName]);
                const data = {
                    'login': this.login,
                    'action': action,
                    'options': {
                        'value': this[fieldName],
                    }
                };

                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            if (data.hasOwnProperty('success') && data.success.status === "NORMAL") {
                                const notificationText =
                                        `'${fieldNameText}' теперь в позиции ${this[fieldName] ? "вкл" : "выкл"}`;
                                Bus.$emit('showNotification', "success", notificationText);
                            } else {
                                const notificationText =
                                        `'${fieldNameText}' не удалось установить в позицию '${this[fieldName] ? "вкл" : "выкл"}'`;
                                Bus.$emit('showNotification', "danger", notificationText);
                                this[fieldName] = !this[fieldName];
                            }
                        });
            },

            // submit sms code for disabling "SMS_CONFIRMATION" feature
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

            // get current wallet security settings
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

            checkCallConfirm(){
                this.callConfirmBlock = false;
            },

            uncheckCallConfirm(){
                console.log(this.callConfirm.checkbox);
//                const data = {
//                    'login': this.login,
//                    'action': "CALL_CONFIRMATION",
//                    'options': {
//                        'value': this.callConfirm.checkbox,
//                    }
//                };
//
//                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
//                        .then((data) => {
//                            console.log(data);
//                            this.smsToken = data.token;
//                        });
            },


//                const data = {
//                    'login': this.login,
//                    'action': "SMS_CONFIRMATION",
//                    'options': {
//                        'value': this.smsConfirmation,
//                        'token': this.smsToken
//                    }
//                };
//
//                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
//                        .then((data) => {
//                            console.log(data);
//                            this.smsToken = data.token;
//                            if (!this.smsConfirmation) {
//                                this.smsConfirmationBlock = true;
//                            }
//                        });
//            },
            callConfirmCheckbox(){
                console.log(this.callConfirm);
                this.callConfirm.smsBlock = !this.callConfirm.checkbox;
                if (this.callConfirm.smsBlock) {
                    uncheckCallConfirm();
                } else {
                    checkCallConfirm();
                }
            },

            // handler for buttons in the top
            showSetting(tabName){
                this.$parent.tab = tabName;
            },
        }
    }
</script>