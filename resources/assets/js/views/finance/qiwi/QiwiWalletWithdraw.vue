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
                        <div class="panel-heading">Вывод средств с Qiwi кошелька ({{ form.login}})</div>
                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Название кошелька</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.name">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Комментарий к переводу</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control"
                                                  v-model="form.comments"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="radio"
                                                       value="wallet"
                                                       v-model="withdrawType">
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
                                                       v-model="withdrawType">
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
                                                       v-model="withdrawType">
                                                Отправить ваучер
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Название кошелька</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.name">
                                    </div>
                                </div>


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
                proxyServer: "",

                form: new Form({
                    useProxy: false,


                    login: this.$route.params.wallet
                }),
            };
        },
        watch: {


            cardNumber(val){
                this.form.autoWithdrawalCardNumber = val.replace(/\s/g, '');
            },

            proxyServer(val) {
                const data = val.split(':');

                this.form.proxy.host = data[0];
                this.form.proxy.port = data[1] ? data[1] : '';
            },
            proxyAuth(val) {
                const data = val.split(':');

                this.form.proxy.login = data.length ? data[0] : '';
                this.form.proxy.password = data[1] ? data[1] : '';
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
                this.$nextTick(() => {
                    $('.tooltip').removeClass('in');
                });

                // get settings of this wallet
                axios.get(`/api/qiwi-wallets/${this.$route.params.wallet}/settings`)
                    .then((response) => {
                        let data = response.data;
                        console.log(data);
                        this.loadAutoWithdrawalOptions(data.autoWithdrawTypes);
                        this.loadWalletTypes(data.walletTypes);
                        let settings = Object.assign(data.walletSettings, data.wallet);
                        settings.proxy = data.proxy;
                        this.loadSettings(settings);
                        console.log(settings);
                    })
            },

            proceed(){

            },

            loadAutoWithdrawalOptions(options){
                options.map((option) => {
                    this.form.autoWithdrawalOptions.push({value: option.slug, text: option.type})
                });

                this.form.autoWithdrawalType = this.form.autoWithdrawalOptions[1].value;
            },

            loadWalletTypes(types){
                let form = this.form;
                types.map((type) => {
                    form.walletTypes.push({value: type.slug, text: type.name})
                });

                form.walletType = form.walletTypes[1].value;
            },

            loadSettings(settings){
                let form = this.form;

                this.proxyServer = settings.proxy.host === null
                    ? ""
                    : settings.proxy.host + ":" + settings.proxy.port;
                this.proxyAuth = settings.proxy.login === null
                    ? ""
                    : settings.proxy.login + "" + ":" + settings.proxy.password;


                form.name = settings.name;
                form.comments = settings.comments;
                form.useProxy = settings.use_proxy;
                form.walletActive = settings.is_active;
                form.alwaysOnline = settings.is_always_online === null ? false : settings.is_always_online;
                form.balanceRecheckTimeout = settings.balance_recheck_timeout;
                form.maximumBalance = settings.maximum_balance;
                form.autoWithdrawalActive = settings.autoWithdrawal_active;
                form.autoWithdrawalTimeout = settings.autoWithdrawal_minutes;
                form.withdrawTarget = settings.autoWithdrawal_target;
                form.usingVouchers = settings.using_vouchers;
                form.autoWithdrawalCardholderName = settings.autoWithdrawal_cardholder_name;
                form.autoWithdrawalCardholderSurname = settings.autoWithdrawal_cardholder_surname;

                if (settings.autoWithdrawal_card_number !== null) {
                    let results = settings.autoWithdrawal_card_number.match(/\d{4}/g);
                    this.cardNumber = results.join(" ");
                } else this.cardNumber = "";


                // selects
                let optionId = settings.autoWithdrawal_type_id === null ? 1 : settings.autoWithdrawal_type_id;
                form.autoWithdrawalType = form.autoWithdrawalOptions[optionId - 1].value;

                form.walletType = this.form.walletTypes[settings.type_id - 1].value;
            },

            saveSettings(){
                console.log(this.form);
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/settings`, this.form)
                    .then((data) => {
                        console.log(data);
                        Bus.$emit('showNotification', "success", "Изменения успешно сохранены");
                    });
            }
        }
    }
</script>