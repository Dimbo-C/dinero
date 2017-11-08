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
                                <div v-if="!smsConfirmation.smsBlock" class="form-group">
                                    <div class="col-sm-offset-1 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="smsConfirmation.checkbox"
                                                       @click="smsConfirmationCheckbox"
                                                       checked>
                                                Подтверждение платежей по СМС
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="smsConfirmation.smsBlock">
                                    <label for="" class="col-sm-12 col-md-3 control-label">Код из смс</label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text"
                                               class="form-control"
                                               v-model="smsConfirmation.code"
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
                                                       v-model="email.checkbox"
                                                       checked
                                                       @click="emailCheckbox">
                                                Привязка email
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="email.inputBlock">
                                    <label for="" class="col-sm-12 col-md-3 control-label">email для привязки</label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text"
                                               class="form-control"
                                               v-model="email.email"
                                               placeholder="email@org.com">
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <button class="btn btn-primary"
                                                @click="emailSendConfirm">
                                            Подтвердить
                                        </button>

                                    </div>
                                </div>

                                <div class="form-group" v-if="email.smsBlock">
                                    <label for="" class="col-sm-12 col-md-3 control-label">Код из смс</label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text"
                                               class="form-control"
                                               v-model="email.smsCode"
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
                                                       @click="smsPaymentCheckbox">Смс-платежи</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="!callConfirm.smsBlock">
                                    <div class="col-sm-offset-1 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="callConfirm.checkbox"
                                                       @click="callConfirmCheckbox"
                                                       checked>Кол-конфирм</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="callConfirm.smsBlock">
                                    <label for="" class="col-sm-12 col-md-3 control-label">Код из смс</label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text"
                                               class="form-control"
                                               v-model="callConfirm.smsCode"
                                               placeholder="123456">
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <button class="btn btn-primary"
                                                @click="callConfirmSmsConfirm">
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
                smsConfirmation: {
                    checkbox: false,
                    smsBlock: false,
                    smsCode: "",
                    token: ""
                },

                email: {
                    email: "",
                    checkbox: false,
                    inputBlock: false,
                    smsBlock: false,
                    smsCode: "",
                    smsToken: ""
                },

                callConfirm: {
                    checkbox: false,
                    smsBlock: false,
                    smsCode: "",
                    token: ""
                },

                useToken: false,
                usePinCode: false,
                smsPayments: false,

                login: this.$route.params.wallet,
                isLoaded: false,
            }
        },
        mounted() {
            this.fetchSettings();
        },
        methods: {

            // handler for checking and un-checking sms confirmation checkbox
            smsConfirmationCheckbox() {
                console.log(this.smsConfirmation.checkbox);
                const data = {
                    'login': this.login,
                    'action': "SMS_CONFIRMATION",
                    'options': {
                        'value': this.smsConfirmation.checkbox,
                        'token': this.smsConfirmation.token
                    }
                };
                if (!this.smsConfirmation.checkbox) {
                    this.smsConfirmation.smsBlock = true;
                }
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            this.smsConfirmation.token = data.token;
                        });
            },

            // submit sms code for disabling "SMS_CONFIRMATION" feature
            confirmSms(){
                const data = {
                    'login': this.login,
                    'action': "SMS_CONFIRMATION",
                    'options': {
                        'code': this.smsConfirmation.code,
                        'token': this.smsConfirmation.token
                    }
                };
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            this.smsConfirmation.checkbox = false;
                            this.smsConfirmation.smsBlock = false;
                        });
            },

            // handler for email checkbox switching
            emailCheckbox(){
                if (this.email.checkbox) {
                    this.email.inputBlock = true;
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
                        'email': this.email.email,
                    }
                };
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            if (data.success) {
                                Bus.$emit('showNotification', "success", "Письмо для подтверждения отправлено на почту");
                                this.email.inputBlock = false;
                            } else {
                                Bus.$emit('showNotification', "danger", "Ошибка, проверьте введенный email");
                            }
                        });
            },

            // get token for unbinding email from wallet (sms is sent to mobile)
            emailFetchUnbindToken(){
                this.email.smsBlock = true;
                const data = {
                    'login': this.login,
                    'action': "EMAIL",
                    'options': {}
                };
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            this.email.smsToken = data.token;
                        });
            },

            // submit code from sms for email
            emailSmsConfirm(){
                const data = {
                    'login': this.login,
                    'action': "EMAIL",
                    'options': {
                        'code': this.email.smsCode,
                        'token': this.email.smsToken
                    }
                };
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            if (data.success) {
                                Bus.$emit('showNotification', "success", `Почта отвязана от этого кошелька`);
                            } else {
                                Bus.$emit('showNotification', "danger", "Ошибка, введенный код неверен");
                                this.email.checkbox = true;
                            }
                            this.email.smsBlock = false;
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

            // get current wallet security settings
            fetchSettings() {
                axios.get(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, {})
                        .then((response) => {
                            console.log(response);
                            const data = response.data;

                            this.callConfirm.checkbox = data.CALL_CONFIRMATION;
                            this.email.checkbox = data.EMAIL;
                            this.smsConfirmation.checkbox = data.SMS_CONFIRMATION;
                            this.usePinCode = data.PIN;
                            this.smsPayments = data.SMS_PAYMENT;
                            this.useToken = data.TOKEN;

                            this.isLoaded = true;
                        });
            },

            // set 'call confirm' to true
            checkCallConfirm(){
                const data = {
                    'login': this.login,
                    'action': "CALL_CONFIRMATION",
                    'options': {
                        'value': this.callConfirm.checkbox,
                    }
                };

                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
//                            if (data.hasOwnProperty('status') && data.status === "NORMAL") {
//                                const notificationText = "'Колл конфирм' включен";
//                                Bus.$emit('showNotification', "success", notificationText);
//                            } else {
//                                const notificationText = "Ошибка при переключении";
//                                Bus.$emit('showNotification', "danger", notificationText);
//                                this.callConfirm.checkbox = !this.callConfirm.checkbox;
//                            }
                        });
            },

            // action on un-checking the 'call confirm' checkbox
            uncheckCallConfirm(){
                console.log(this.callConfirm.checkbox);
                const data = {
                    'login': this.login,
                    'action': "CALL_CONFIRMATION",
                    'options': {
                        'value': this.callConfirm.checkbox,
                    }
                };

                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            this.callConfirm.token = data.token;
                        });
            },

            // submit code from sms for 'call confirm'
            callConfirmSmsConfirm(){
                console.log("call confirm");
                const data = {
                    'login': this.login,
                    'action': "CALL_CONFIRMATION",
                    'options': {
                        'code': this.callConfirm.smsCode,
                        'token': this.callConfirm.token
                    }
                };
                console.log(data);
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
                        .then((data) => {
                            console.log(data);
                            if (data.success) {
                                Bus.$emit('showNotification', "success", `Подтверждение по смс отключено`);
                            } else {
                                Bus.$emit('showNotification', "danger", "Ошибка, введенный код неверен");
                                this.email.checkbox = true;
                            }
                            this.callConfirm.smsBlock = false;
                        });
            },


//                const data = {
//                    'login': this.login,
//                    'action': "SMS_CONFIRMATION",
//                    'options': {
//                        'value': this.smsConfirmation.checkbox,
//                        'token': this.smsConfirmation.token
//                    }
//                };
//
//                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/security`, new Form(data))
//                        .then((data) => {
//                            console.log(data);
//                            this.smsConfirmation.token = data.token;
//                            if (!this.smsConfirmation.checkbox) {
//                                this.smsConfirmation.checkboxBlock = true;
//                            }
//                        });
//            },
            callConfirmCheckbox(){
                console.log(this.callConfirm);
                this.callConfirm.smsBlock = !this.callConfirm.checkbox;
                if (this.callConfirm.smsBlock) {
                    this.uncheckCallConfirm();
                } else {
                    this.checkCallConfirm();
                }
            },

            // handler for buttons in the top
            showSetting(tabName){
                this.$parent.tab = tabName;
            },
        }
    }
</script>