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
        <loading :show="processed"></loading>
        <div v-if="!processed" class="container-fluid">
            <div class="row m-b-lg">
                <div class="col-sm-10">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-12 col-md-10">
                                    Вывод средств с Qiwi кошелька ({{ form.login }})
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <router-link tag="span" :to="'/finance/qiwi/'+form.login+'/egg'">
                                        <a>Ваучеры</a>
                                    </router-link>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body" v-if="!resultObtained">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Сумма</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.sum">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Комментарий к переводу</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control"
                                                  v-model="form.comment"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="radio"
                                                       value="wallet"
                                                       id="withdraw.wallet"
                                                       v-model="switcher">
                                                Перевод на Qiwi кошелек
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="radio"
                                                       value="card"
                                                       id="withdraw.card"
                                                       v-model="switcher">
                                                Перевод на банковскую карту
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!--<div class="form-group">-->
                                <!--<div class="col-sm-offset-4 col-sm-8">-->
                                <!--<div class="checkbox">-->
                                <!--<label>-->
                                <!--<input type="radio"-->
                                <!--value="voucher"-->
                                <!--id="withdraw.voucher"-->
                                <!--v-model="switcher">-->
                                <!--Активировать/Купить ваучер-->
                                <!--</label>-->
                                <!--</div>-->
                                <!--</div>-->
                                <!--</div>-->

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ label }}</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               :placeholder="placeholder"
                                               v-model="form.targetField">
                                        <span class="help-block">{{ underTip}}</span>
                                    </div>
                                </div>


                                <template v-if="form.withdrawType=='card'">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Фамилия получателя</label>
                                        <div class="col-sm-8">
                                            <input type="text"
                                                   class="form-control"
                                                   placeholder="SEMENOV"
                                                   v-model="form.cardholderName">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Имя получателя</label>
                                        <div class="col-sm-8">
                                            <input type="text"
                                                   class="form-control"
                                                   placeholder="SEMEN"
                                                   v-model="form.cardholderSurname">
                                        </div>
                                    </div>
                                </template>


                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <button class="btn btn-primary"
                                                @click="proceed">
                                            Продолжить
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="panel-body" v-if="resultObtained">
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
                types: {
                    card: {
                        "label": "Номер карты",
                        "placeholder": "Например: 1234 5678 9012 3456",
                        "underTip": "Номер банковской карты, на которую вы хотите перевести деньги"
                    },
                    wallet: {
                        "label": "Номер кошелька",
                        "placeholder": "Например: +71234567890",
                        "underTip": "Номер кошелька, на который вы хотите перевести деньги"
                    },
//                    voucher: {
//                        "label": "Код ваучера для активации",
//                        "placeholder": "Например: L5MQLT8PH8339M715NE6K1PKD",
//                        "underTip": "Оставьте поле пустым, чтобы создать ваучер на указанную сумму"
//                    },
                },
                switcher: "",
                proxyServer: "",
                label: "",
                placeholder: "",
                underTip: "",
                balance: 0,
                responseText: "",
                processed: false,
                resultObtained: false,
                notificationClass: "alert-danger",
                updatedBalance: "(Загружается... )",

                form: new Form({
                    sum: 0,
                    comment: "",
                    targetField: "",
                    withdrawType: "",
                    cardholderName: "",
                    cardholderSurname: "",

                    login: this.$route.params.wallet

                }),
            };
        },

        watch: {
            switcher(val){
                this.form.withdrawType = val;
                this.label = this.types[val].label;
                this.placeholder = this.types[val].placeholder;
                this.underTip = this.types[val].underTip;
            },
        },

        mounted() {
            this.prepareComponent();
        },

        methods: {
            back(){
                this.resultObtained = false;
            },
            prepareComponent() {
                this.initBalance();
                this.switcher = "wallet";

                this.$nextTick(() => {
                    $('.tooltip').removeClass('in');
                });
            },
            proceed() {
                console.log(this.form);
                this.processed = true;
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/withdraw`, this.form)
                    .then((data) => {
                        let notificationType = data.status == 200 ? "success" : "danger";
                        this.notificationClass = "alert-" + notificationType;
                        this.resultObtained = true;
                        this.responseText = data.resultText;
                        this.processed = false;
                        this.updateWallet(this.$route.params.wallet);
                    });
            },
            initBalance(){
                axios.get(`/api/qiwi-wallets/${this.$route.params.wallet}/settings`)
                    .then((response) => {
                        const balance = response.data.wallet.balance;
                        this.form.sum = balance > 0 ? 1 : 0;
                    })
            },
            updateWallet(login) {
                let auth = {"login": login};
                Dinero.post('/api/qiwi-wallets/update-balance', new Form(auth))
                    .then((balance) => {
                        this.form.sum = balance;
                        this.updatedBalance = balance;
                    })
            },
        }
    }
</script>