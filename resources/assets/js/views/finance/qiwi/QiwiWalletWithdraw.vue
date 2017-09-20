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
                        <div class="panel-heading">Вывод средств с Qiwi кошелька ({{ form.login }})</div>
                        <div class="panel-body">
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

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="radio"
                                                       value="voucher"
                                                       id="withdraw.voucher"
                                                       v-model="switcher">
                                                Отправить ваучер
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ label }}</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               :placeholder="placeholder"
                                               v-model="form.targetField">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                types: {
                    card: {
                        "label": "Номер карты",
                        "placeholder": "Например: 1234 5678 9012 3456"
                    },
                    wallet: {
                        "label": "Номер кошелька",
                        "placeholder": "Например: +71234567890"
                    },
                    voucher: {
                        "label": "email получателя ваучера",
//                        "placeholder": "Например: L5MQLT8PH8339M715NE6K1PKD"
                        "placeholder": "Например: name.surname@gmail.com"
                    },
                },
                switcher: "",
                proxyServer: "",
                label: "",
                placeholder: "",
                balance: 0,

                form: new Form({
                    sum: 0,
                    comment: "",
                    targetField: "5168 7422 0767 3892",
                    withdrawType: "",
                    cardholderName: "VLAD",
                    cardholderSurname: "GORBATKO",

                    login: this.$route.params.wallet

                }),
            };
        },

        watch: {
            switcher(val){
                this.form.withdrawType = val;
                this.label = this.types[val].label;
                this.placeholder = this.types[val].placeholder;
            },
        },

        /**
         * Prepare the component.
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.initBalance();
                this.switcher = "wallet";
            },
            proceed() {
                console.log(this.form);
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/withdraw`, this.form)
                    .then((data) => {
                        console.log(data);
                        let notificationType = data.status == 200 ? "success" : "danger";
                        Bus.$emit('showNotification', notificationType, data.resultText);
                        this.updateWallet(this.$route.params.wallet);
                    });
            },
            initBalance(){
                axios.get(`/api/qiwi-wallets/${this.$route.params.wallet}/settings`)
                    .then((response) => {
                        let data = response.data;
                        this.form.sum = data.wallet.balance;
                    })
            },
            updateWallet(login) {
                let auth = {"login": login};
                Dinero.post('/api/qiwi-wallets/update', new Form(auth))
                    .then((data) => {
                        console.log(data);
                        this.form.sum = data.balance;
                    })
            },
        }
    }
</script>