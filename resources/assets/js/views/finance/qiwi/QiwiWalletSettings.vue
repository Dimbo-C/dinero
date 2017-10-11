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
                                    <label for="" class="col-sm-4 control-label">Минимальный баланс</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.minimumBalance">
                                        <span class="help-block">Минимальный баланс кошелька.
                                            Работает на автовыводе.
                                            Оставляет сумму на балансе кошелька.
                                            Для отключения введите 0.
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Баланс для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.autoWithdrawalMinBalance">
                                        <span class="help-block">Работает только если кошелек настроен на автовывод.
                                            Автовывод срабатывает если баланс кошелька больше либо равен указанной сумме.
                                            Для отключения введите 0.
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Лимит транзакций на вывод</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.autoWithdrawalLimit">
                                        <span class="help-block">Работает только если кошелек настроен на автовывод.
                                            За 1 раз будет выведена сумма, не превышающая данное значение.
                                            Для отключения введите 0.
                                        </span>
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
                                        <select class="form-control"
                                                v-model="form.autoWithdrawalType">
                                            <option v-for="o in form.autoWithdrawalTypes"
                                                    :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Тип автовывода</label>
                                    <div class="col-sm-8">
                                        <select class="form-control"
                                                v-model="form.autoWithdrawalTarget">
                                            <option v-for="o in form.autoWithdrawalTargets"
                                                    :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for=""
                                           class="col-sm-4 control-label"
                                           v-model="form.autoWithdrawalType">
                                        Вызывать автовывод каждые X минут
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.autoWithdrawalTimeout">
                                        <span class="help-block">Работает только когда кошелек настроен на автовывод.
                                            Как только с момента последнего автовывода прошло указанное количество минут,
                                            вызывается автовывод, если режим автовывода указан <b>Каждые Х минут</b>.
                                            </span>
                                    </div>
                                </div>

                                <!--<div class="form-group">-->
                                <!--<label for="" class="col-sm-4 control-label">Минимальная сума для автовывода</label>-->
                                <!--<div class="col-sm-8">-->
                                <!--<input type="text"-->
                                <!--class="form-control"-->
                                <!--v-model="form.minimumAutoWithdrawAmount"-->
                                <!--:disabled="!form.autoWithdrawalActive">-->
                                <!--<span class="help-block">Минимальный баланс кошелька при котором должен быть совершен вывод</span>-->
                                <!--</div>-->
                                <!--</div>-->

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

                                <!--<div class="form-group">-->
                                <!--<div class="col-sm-offset-4 col-sm-8">-->
                                <!--<div class="checkbox">-->
                                <!--<label>-->
                                <!--<input type="radio"-->
                                <!--value="wallet"-->
                                <!--v-model="form.withdrawTarget">-->
                                <!--Автовывод с помощью ваучеров-->
                                <!--</label>-->
                                <!--</div>-->
                                <!--</div>-->
                                <!--</div>-->

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Кошелек для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: +79123456789"
                                               v-model="form.autoWithdrawalWallet">
                                    </div>
                                </div>

                                <!--<div class="form-group">-->
                                <!--<div class="col-sm-offset-4 col-sm-8">-->
                                <!--<div class="checkbox">-->
                                <!--<label>-->
                                <!--<input type="radio"-->
                                <!--value="card"-->
                                <!--v-model="form.withdrawTarget">-->
                                <!--Автовывод на карту-->
                                <!--</label>-->
                                <!--</div>-->
                                <!--</div>-->
                                <!--</div>-->

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Карта для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="cardNumber"
                                               placeholder="XXXX XXXX XXXX XXXX">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Данные владельца карты</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawalCardholderName"
                                               placeholder="Имя">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawalCardholderSurname"
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
                    autoWithdrawalTypes: [],
                    autoWithdrawalTarget: "",
                    autoWithdrawalTargets: [
                        {value: "card", text: "На банковскую карту VISA/MASTERCARD"},
                        {value: "wallet", text: "На Qiwi кошелек"}
                    ],

                    autoWithdrawalTimeout: 0,
                    autoWithdrawalLimit: 14500,
                    autoWithdrawalMinBalance: 2500, // bottom limiter for auto withdrawals

                    minimumBalance: 0,  // balance to leave on wallet
                    autoWithdrawalCardNumber: "",
                    autoWithdrawalCardholderName: "",
                    autoWithdrawalCardholderSurname: "",
                    autoWithdrawalWallet: "",
                    usingVouchers: false,
                    withdrawTarget: "card",

                    proxy: {
                        host: "",
                        port: "",
                        login: "",
                        password: ""
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

                this.loadData();
            },

            loadData(){
                // get settings of this wallet
                axios.get(`/api/qiwi-wallets/${this.$route.params.wallet}/settings`)
                    .then((response) => {
                        let data = response.data;
                        this.loadAutoWithdrawalTypes(data.autoWithdrawTypes);
                        this.loadWalletTypes(data.walletTypes);
                        let settings = Object.assign(data.walletSettings, data.wallet);
                        settings.proxy = data.proxy;
                        this.loadSettings(settings);

                        console.log(data);
                        console.log(settings);
                    })
            },

            loadAutoWithdrawalTypes(options){
                options.map((option) => {
                    this.form.autoWithdrawalTypes.push({value: option.slug, text: option.type})
                });

                this.form.autoWithdrawalType = this.form.autoWithdrawalTypes[1].value;
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
                form.autoWithdrawalWallet = settings.autoWithdrawal_wallet_number;
                form.autoWithdrawalMinBalance = settings.autoWithdrawal_minimum_withdraw_amount;
                form.autoWithdrawalLimit = settings.autoWithdrawal_limit;

                if (settings.autoWithdrawal_card_number !== null) {
                    let results = settings.autoWithdrawal_card_number.match(/\d{4}/g);
                    this.cardNumber = results.join(" ");
                } else this.cardNumber = "";


                // selects
                let optionId = settings.autoWithdrawal_type_id === null ? 1 : settings.autoWithdrawal_type_id;
                form.autoWithdrawalType = form.autoWithdrawalTypes[optionId - 1].value;

                form.walletType = this.form.walletTypes[settings.type_id - 1].value;

                this.form.autoWithdrawalTarget = this.form.autoWithdrawalTargets[0].value;
                this.form.autoWithdrawalTarget = settings.autoWithdrawal_target;
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