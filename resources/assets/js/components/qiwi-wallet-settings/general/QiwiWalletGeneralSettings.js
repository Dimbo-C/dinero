export default {
    /*
     * The component's data.
     */
    data() {
        return {
            tab: "security",
            proxyServer: "",
            proxyAuth: "",
            cardNumber: "",
            alert: {
                show: false,
                className: "alert-success",
                text: "",
            },
            autoWithdrawalWallets: [],
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
                    {value: "wallet", text: "На Qiwi кошелек"},
                    {value: "withdrawals", text: "На свободный кошелек для автовывода"},
                    {value: "withdrawals_card", text: "На свободный кошелек 'Автовывод\\карта'"},
                    {value: "withdrawals_wallet", text: "На свободный кошелек 'Автовывод\\номер'"},
                ],

                autoWithdrawalTimeout: 0,
                autoWithdrawalLimit: 14500, // maximum auto withdraw amount
                autoWithdrawalMinBalance: 2500, // bottom limiter for auto withdrawals

                minimumBalance: 0,  // balance to leave on wallet after any withdraw
                autoWithdrawalCardNumber: "",
                autoWithdrawalCardholderName: "",
                autoWithdrawalCardholderSurname: "",
                autoWithdrawalWallet: "",
                autoWithdrawalWallets: [],
                usingVouchers: false,
                withdrawTarget: "card", // basic withdraw target

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
        autoWithdrawalWallets(val) {
            let wallets = (val === "" || val === null) ? [] : val.split(/[\s;,]+/g);
            console.log(wallets);
            this.form.autoWithdrawalWallets = wallets;

            console.log(this.form.autoWithdrawalWallets);

        },
        cardNumber(val) {
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

        // did it with jQuery because in Vue it requires to bind that event to all input
        $('.container-fluid').keydown(event => {
            if (event.key === "Enter") {
                this.saveSettings();
            }
        })
    },

    methods: {
        /**
         * Prepare the component.
         */
        prepareComponent() {
            this.loadData();
        },

        loadData() {
            // get settings of this wallet
            axios.get(`/api/qiwi-wallets/${this.$route.params.wallet}/settings`)
                .then((response) => {
                    let data = response.data;
                    let settings = Object.assign(data.walletSettings, data.wallet);
                    settings.proxy = data.proxy;

                    this.loadAutoWithdrawalTypes(data.autoWithdrawTypes);
                    this.loadWalletTypes(data.walletTypes);
                    this.loadSettings(settings);
                })
        },

        loadAutoWithdrawalTypes(options) {
            options.map((option) => {
                this.form.autoWithdrawalTypes.push({value: option.slug, text: option.type})
            });

            this.form.autoWithdrawalType = this.form.autoWithdrawalTypes[1].value;
        },

        loadWalletTypes(types) {
            let form = this.form;
            types.map((type) => {
                form.walletTypes.push({value: type.slug, text: type.name})
            });

            form.walletType = form.walletTypes[1].value;
        },

        loadSettings(settings) {
            console.log("settings:");
            console.log(settings);
            let form = this.form;

            this.proxyServer = settings.proxy.host === null
                ? "" : settings.proxy.host + ":" + settings.proxy.port;
            this.proxyAuth = settings.proxy.login === null
                ? "" : settings.proxy.login + "" + ":" + settings.proxy.password;

            form.name = settings.name;
            form.comments = settings.comments;
            form.useProxy = settings.use_proxy;
            form.walletActive = settings.is_active;
            form.alwaysOnline = settings.is_always_online === null ? false : settings.is_always_online;
            form.balanceRecheckTimeout = settings.balance_recheck_timeout;
            form.maximumBalance = settings.maximum_balance;
            form.minimumBalance = settings.minimum_balance;
            form.autoWithdrawalActive = settings.autoWithdrawal_active;
            form.autoWithdrawalTimeout = settings.autoWithdrawal_minutes;
            form.withdrawTarget = settings.autoWithdrawal_target;
            form.usingVouchers = settings.using_vouchers;
            form.autoWithdrawalCardholderName = settings.autoWithdrawal_cardholder_name;
            form.autoWithdrawalCardholderSurname = settings.autoWithdrawal_cardholder_surname;

            this.autoWithdrawalWallets = settings.autoWithdrawal_wallet_numbers;

            form.autoWithdrawalMinBalance = settings.autoWithdrawal_minimum_withdraw_amount;
            form.autoWithdrawalLimit = settings.autoWithdrawal_limit;

            if (settings.autoWithdrawal_card_number !== null) {
                this.cardNumber = settings.autoWithdrawal_card_number;
            } else this.cardNumber = "";


            // selects
            let optionId = settings.autoWithdrawal_type_id === null ? 1 : settings.autoWithdrawal_type_id;
            form.autoWithdrawalType = form.autoWithdrawalTypes[optionId - 1].value;

            form.walletType = this.form.walletTypes[settings.type_id - 1].value;

            this.form.autoWithdrawalTarget = this.form.autoWithdrawalTargets[0].value;
            this.form.autoWithdrawalTarget = settings.autoWithdrawal_target;
        },

        saveSettings() {
            console.log(this.form);
            Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/settings`, this.form)
                .then((data) => {
                    console.log(data);
                    this.showAlert("alert-success", "Изменения успешно сохранены");
                })
                .catch(() => {
                    this.showAlert("alert-danger", "Не удалось сохранить настройки");
                });
            this.scrollToTop();
        },

        showAlert(className, text) {
            //                        Bus.$emit('showNotification', "success", "Изменения успешно сохранены");
            this.alert.show = true;
            this.alert.className = className;
            this.alert.text = text;
        },

//            triggerAutoWithdraw(){
//                axios.post(`/api/qiwi-wallets/${this.$route.params.wallet}/auto-withdraw`, this.form)
//                        .then(response => {
//                            console.log(response.data);
//                            Bus.$emit('showNotification', "success", response.data.message);
//                        })
//                        .catch(error => {
//                            console.log(error.response);
//                            Bus.$emit('showNotification', "danger", error.response.data.message);
//                        });
//            },

        scrollToTop() {
            $("html, body").animate({
                scrollTop: 0
            }, 600);
        },
        showSetting(tabName) {
            this.$parent.tab = tabName;
        },
    }
}