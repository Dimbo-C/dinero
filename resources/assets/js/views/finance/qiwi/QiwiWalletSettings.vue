<template>
    <div>
        <page-header icon="fa-money" title="Настройки кошелька Qiwi">
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
                        <div class="panel-heading">Настройки кошелька Qiwi ({{ form.login}})</div>
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
                                    <label class="col-sm-4 control-label">Комментарий к кошельку</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control"
                                                  v-model="form.comments"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.useProxy">
                                                Использовать прокси
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="form.useProxy">
                                    <label for="" class="col-sm-4 control-label">Прокси сервер</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="proxyServer"
                                               placeholder="host:port">
                                    </div>
                                </div>

                                <div class="form-group" v-if="form.useProxy">
                                    <label for="" class="col-sm-4 control-label">Авторизация прокси</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               :disabled="!form.useProxy"
                                               v-model="proxyAuth"
                                               placeholder="login:password">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="form.walletActive"
                                                       checked>
                                                Кошелек активен
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="form.alwaysOnline"
                                                       checked>
                                                Режим «Всегда онлайн»
                                            </label>
                                        </div>
                                        <span class="help-block">Поставьте галочку,
                                            если хотите чтобы сессия кошелька всегда поддерживалась в режиме
                                            онлайн. Аналогично постоянному нахождению в браузере.
                                            Если галочка не стоит - сессия будет слетать и кошелек будет перелогиниваться.
                                            Не используйте для всех кошельков поголовно, только для автовыводных и приемных,
                                            где своевременное снятие денег в приоритете.
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Частота проверки баланса, мин.</label>
                                    <div class="col-sm-8">
                                        <input type="number" min="0"
                                               v-model="form.balanceRecheckTimeout"
                                               class="form-control">
                                        <span class="help-block">Укажите через какое количество минут система должна автоматически
                                        обновлять баланс кошелька. Чтобы отключить функцию введите 0</span>
                                    </div>
                                </div>

                                <!--<div class="form-group">-->
                                <!--<label for="" class="col-sm-4 control-label">Тип кошелька</label>-->
                                <!--<div class="col-sm-8">-->
                                <!--<input type="text" class="form-control">-->
                                <!--</div>-->
                                <!--</div>-->

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Тип кошелька</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" v-model="form.walletType">
                                            <option v-for="o in form.walletTypes" :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Максимальный баланс</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.maximumBalance">
                                        <span class="help-block">Максимальный баланс кошелька, при достижении которого
                                            кошелек автоматически уходит в резервные</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.autoWithdrawalActive">
                                                Автовывод включен
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Режим работы автовывода</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" v-model="form.autoWithdrawalType"
                                                :disabled="!form.autoWithdrawalActive">
                                            <option v-for="o in form.autoWithdrawalOptions" :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" v-if="form.autoWithdrawalType === 'every_x_minutes'">
                                    <label for=""
                                           class="col-sm-4 control-label"
                                           v-model="form.autoWithdrawalType">
                                        Вызывать автовывод каждые X минут
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.autoWithdrawalTimeout">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Минимальная сума для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.minimumAutoWithdrawAmount"
                                               :disabled="!form.autoWithdrawalActive">
                                        <span class="help-block">Минимальный баланс кошелька при котором должен быть совершен вывод</span>
                                    </div>
                                </div>

                                <!--<div class="form-group">-->
                                <!--<div class="col-sm-offset-4 col-sm-8">-->
                                <!--<div class="checkbox">-->
                                <!--<label>-->
                                <!--<input type="radio" v-model="form.usingVouchers">-->
                                <!--Автовывод с помощью ваучеров-->
                                <!--</label>-->
                                <!--</div>-->
                                <!--</div>-->
                                <!--</div>-->

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="radio"
                                                       value="wallet"
                                                       v-model="form.withdrawTarget">
                                                Автовывод с помощью ваучеров
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Кошельки для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="+79123456789;+79111111111"
                                               :disabled="form.withdrawTarget!='wallet'"
                                               v-model="form.autoWithdrawalWallets">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="radio"
                                                       value="card"
                                                       v-model="form.withdrawTarget">
                                                Автовывод на карту
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Карта для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="cardNumber"
                                               placeholder="XXXX XXXX XXXX XXXX"
                                               :disabled="form.withdrawTarget!='card'">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Данные владельца карты</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawalCardholderName"
                                               :disabled="form.withdrawTarget!='card'"
                                               placeholder="Имя">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawalCardholderSurname"
                                               :disabled="form.withdrawTarget!='card'"
                                               placeholder="Фамилия">
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <button class="btn btn-primary"
                                                @click="saveSettings">
                                            Сохранить
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
                proxyAuth: "",
                cardNumber: "",
                form: new Form({
                    useProxy: false,
                    name: "",
                    comments: "",
                    walletActive: false,
                    walletType: "",
                    walletTypes: [],
                    alwaysOnline: false,
                    balanceRecheckTimeout: 0,
                    maximumBalance: 100,
                    autoWithdrawalActive: true,
                    autoWithdrawalType: "",
                    autoWithdrawalOptions: [],
                    autoWithdrawalTimeout: 0,
                    minimumAutoWithdrawAmount: 2500,
                    autoWithdrawalCardNumber: "",
                    autoWithdrawalCardholderName: "",
                    autoWithdrawalCardholderSurname: "",
                    usingVouchers: false,
                    withdrawTarget: "card",

                    proxy: {
                        host: "",
                        port: "",
                        login: "",
                        password: "",
                    },
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