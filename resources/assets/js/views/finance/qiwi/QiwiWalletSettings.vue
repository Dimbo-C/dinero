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
                        <div class="panel-heading">Настройки кошелька Qiwi</div>
                        <div class="panel-body">
                            <div class="form-horizontal">
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
                                                <input type="checkbox" v-model="useProxy">
                                                Использовать прокси
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="useProxy">
                                    <label for="" class="col-sm-4 control-label">Прокси сервер</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="proxyServer"
                                               placeholder="host:port">
                                    </div>
                                </div>

                                <div class="form-group" v-if="useProxy">
                                    <label for="" class="col-sm-4 control-label">Авторизация прокси</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               :disabled="!useProxy"
                                               v-model="proxyAuth"
                                               placeholder="login:password">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="form.wallet_active"
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
                                                       v-model="form.always_online"
                                                       checked>
                                                Режим «Всегда онлайн»
                                            </label>
                                        </div>
                                        <span class="help-block">Поставьте галочку, если хотите чтобы сессия кошелька всегда поддерживалась в режиме
                                        онлайн.</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Частота проверки баланса, мин.</label>
                                    <div class="col-sm-8">
                                        <input type="number" min="0"
                                               v-model="form.balance_recheck_timeout"
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
                                        <select class="form-control" v-model="form.wallet_type">
                                            <option v-for="o in form.wallet_types" :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Максимальный баланс</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.maximum_balance">
                                        <span class="help-block">Максимальный баланс кошелька, при достижении которого
                                            кошелек автоматически уходит в резервные</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.autoWithdrawal_active">
                                                Автовывод включен
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Режим работы автовывода</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" v-model="form.autoWithdrawal_type"
                                                :disabled="!form.autoWithdrawal_active">
                                            <option v-for="o in form.autoWithdrawal_options" :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" v-if="form.autoWithdrawal_type === 'every_x_minutes'">
                                    <label for=""
                                           class="col-sm-4 control-label"
                                           v-model="form.autoWithdrawal_type">
                                        Вызывать автовывод каждые X минут
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.autoWithdrawal_timeout">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.using_vouchers">
                                                Автовывод с помощью ваучеров
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Карта для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawal_card_number">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Данные владелца карты</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawal_cardholder_name"
                                               placeholder="Имя">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawal_cardholder_surname"
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
                useProxy: false,
                proxyServer: "",
                proxyAuth: "",
                form: new Form({
                    comments: '',
                    wallet_active: false,
                    wallet_type: '',
                    wallet_types: [],
                    always_online: false,
                    balance_recheck_timeout: 0,
                    maximum_balance: 100,
                    autoWithdrawal_active: true,
                    autoWithdrawal_type: '',
                    autoWithdrawal_options: [],
                    autoWithdrawal_timeout: 0,
                    autoWithdrawal_card_number: "",
                    autoWithdrawal_cardholder_name: "",
                    autoWithdrawal_cardholder_surname: "",
                    using_vouchers: false,

                    proxy: {
                        host: '',
                        port: '',
                        login: '',
                        password: '',
                    },

                    login: this.$route.params.wallet

                }),
            };
        },
        watch: {
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
                        this.loadAutoWithdrawalOptions(data.autowithdraw_types);
                        this.loadWalletTypes(data.wallet_types);
                        var settings = Object.assign(data.wallet_settings, data.wallet);
                        this.loadSettings(settings);
                        console.log(settings);
                    })
            },

            loadAutoWithdrawalOptions(options){
                options.map((option) => {
                    this.form.autoWithdrawal_options.push({value: option.slug, text: option.type})
                });

                this.form.autoWithdrawal_type = this.form.autoWithdrawal_options[1].value;
            },

            loadWalletTypes(types){
//                var form=this.form
                types.map((type) => {
                    this.form.wallet_types.push({value: type.slug, text: type.name})
                });

                this.form.wallet_type = this.form.wallet_types[1].value;
            },

            loadSettings(settings){
                var form = this.form;

                form.comments = settings.comments;
                this.useProxy = settings.proxy_id !== null;
                form.wallet_active = settings.is_active;
                form.always_online = settings.is_always_online === null ? false : settings.is_always_online;
                form.balance_recheck_timeout = settings.balance_recheck_timeout;
                form.maximum_balance = settings.maximum_balance;
                form.autoWithdrawal_active = settings.autoWithdrawal_active;
                form.using_vouchers = settings.using_vouchers;
                form.autoWithdrawal_card_number = settings.autoWithdrawal_card_number;
                form.autoWithdrawal_cardholder_name = settings.autoWithdrawal_cardholder_name;
                form.autoWithdrawal_cardholder_surname = settings.autoWithdrawal_cardholder_surname;

                // selects
                let optionId = settings.autoWithdrawal_type_id === null ? 1 : settings.autoWithdrawal_type_id;
                form.autoWithdrawal_option = this.form.autoWithdrawal_options[optionId - 1];

                form.wallet_type = this.form.wallet_types[settings.type_id - 1].value;
            },


            saveSettings(){
                this.form.use_proxy = this.useProxy;
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