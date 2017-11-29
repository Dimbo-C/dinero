import Table from '../../../../mixins/table';
import * as _ from "lodash/array";

export default {
    mixins: [Table],
    props: ['type', 'types', 'exclude', 'is-inactive'],
    data() {
        return {
            noSum: "...",
            moveTo: this.types.filter(t => t.id !== this.type.id)[0].id,
            foo: '',
            onChangeSelect: '',
            spinners: [],
            withdrawers: [],
            sort: {
                column: "name",
                order: 1 // 1 - desc, 0 - asc
            }
        };
    },

    mounted() {
        this.sort.column = Vue.ls.get('sort.column') || "name";
        this.sort.order = Vue.ls.get('sort.order') || 0;

        this.items = this.type.wallets;
        this.sorter();
    },

    created: function () {
        this.$parent.$on('update', (wallets) => {
            const walletsToUpdate = _.intersection(wallets, this.type.wallets.map(wallet => wallet.login));
            this.selected = [];
            walletsToUpdate.forEach(wallet => this.updateWallet(wallet));
        });
    },

    watch: {
        selected(val) {
            this.$emit('updateSelected', val);
        },

        // on this element change - run sorter again (to sort updated(received) values
        type: function () {
            this.sorter();
        }


    },

    methods: {
        setSortField(fieldName) {
            this.sort.order = (fieldName == this.sort.column)
                ? !this.sort.order
                : this.sort.order;

            this.sort.column = fieldName;

            this.sorter();

            Vue.ls.set('sort.column', this.sort.column);
            Vue.ls.set('sort.order', +this.sort.order);

        },

        sorter() {
            const colName = this.sort.column;
            const prior = this.sort.order == 0 ? -1 : 1;
            this.type.wallets = this.type.wallets.sort((w1, w2) => {
                const cardNum1 = w1.settings.autoWithdrawal_card_number;
                const cardNum2 = w2.settings.autoWithdrawal_card_number;

                switch (colName) {
                    case "name":
                        return (w1.name < w2.name) ? prior : -prior;
                    case "number":
                        return (w1.login < w2.login) ? prior : -prior;
                    case "visa":
                        return (cardNum1 < cardNum2) ? prior : -prior;
                    case "balance":
                        return (this.moneysToFloat(w1.balance) < this.moneysToFloat(w2.balance))
                            ? prior : -prior;
//                            return this.bigMoneyCompare(w1.balance, w2.balance) ? prior : -prior;
                    case "income":
                        return (
                            this.moneysToFloat(w1.month_income) < this.moneysToFloat(w2.month_income))
                            ? prior : -prior;
//                            return this.bigMoneyCompare(w1.month_income, w2.month_income) ? prior : -prior;
                }
            })

        },

        moveWallets() {
            const moveFrom = this.isInactive
                ? this.selected[0].type_id
                : this.type.id;

            this.$emit('moveWallets', this.selected, moveFrom, this.moveTo)
        },

        removeWallet(login) {
            this.$router.push({path: `/finance/qiwi/remove/${login}`});
        },

        updateBalance(login) {
            this.spinners.push(login);
            let auth = {"login": login};
            Dinero.post('/api/qiwi-wallets/update-balance', new Form(auth))
                .then((response) => {
                    const balance = response.balance;
                    console.log("Balance: " + balance);
                    this.updateBalanceCallback(login, balance);
                })
                .catch(error => {
//                            console.log(error.response);
                    this.updateBalanceCallback(login);
                });
        },

        updateBalanceCallback(login, balance = null) {
            this.items.map((wallet) => {
                if (wallet.login === login) {

                    console.log("Balance in callback", balance);
                    wallet.balance = this.tidySum(balance);

                    if (balance == null) {
                        wallet.settings.failed_attempts += 1;
                    } else {
                        wallet.settings.failed_attempts = 0;
                    }

//                        this.$nextTick(() => {
                    $('[data-toggle="tooltip"][login="' + login + '"]')
                        .tooltip()
                        .attr("data-original-title", this.attemptPopup(wallet.settings.failed_attempts));
//                        });

                    this.spinners = this.spinners.filter((elem) => login !== elem);
                }
            });
        },

        updateIncome(login) {
            let auth = {"login": login};
            Dinero.post('/api/qiwi-wallets/update-income', new Form(auth))
                .then((response) => {
                    // TODO: mb some notification on update (it is really dispatched as a job, so result is not immediate)
                })
        },

        updateWallet(login) {
            this.updateBalance(login);
            this.updateIncome(login);
        },

        autoWithdrawWallet(login) {
            this.withdrawers.push(login);

            axios.post(`/api/qiwi-wallets/${login}/auto-withdraw`)
                .then(response => {
                    Bus.$emit('showNotification', "success", "Автовывод успешно проведен");
                })
                .catch(error => {
                    const status = error.response.status;
                    if (status === 400) {
                        Bus.$emit('showNotification', "danger", "Не удалось провести автовывод, проверьте баланс кошелька и настройки");
                    } else if (status === 500) {
                        Bus.$emit('showNotification', "danger", "Ошибка сервера, попробуйте позже");
                    }
                })
                .finally(() => {
                    this.items.map((item) => {
                        if (item.login === login) {
                            this.withdrawers = this.withdrawers.filter((elem) => login !== elem);
                        }
                    });
                })
        },

        spinnerSwitcher(balance, login) {
            if (this.spinners.includes(login)) {
                return "...";
            } else {
                return this.tidySum(balance) + " " + Dinero.currencySymbol;
            }
        },

        // TODO: REFACTOR ASAP, LITERAL GARBAGE
        tidySum(sum) {
            sum = "" + sum;
            sum = sum.replace(/,/g, "");
            if (isNaN(sum) || +sum === -1) {
                console.log("Number : ", sum, " is ?");
                return "?";
            } else {
//                    console.log("Sum inc", sum);
                let str = (typeof sum === "object") ? "0.00" : (sum + "");
                if (str === "") str = "0.00";

                str = str.replace(/,/g, "");
//                    console.log("Sum out", str);
                str = parseFloat(str).toFixed(2);

                return str;
            }
        },

        moneysToFloat(moneys){
            // remove commas from moneystring
            return parseFloat(moneys.replace(/[, ]/g, ""));
        },

        attemptClass(attempts){
            if (attempts > 5) return "attempts-danger";
            else if (attempts > 0) return "attempts-warning";
            else return "attempts-ok";
        },

        attemptPopup(attempts){
            return "Неудачных входов: " + attempts;
        }


    },
    computed: {
        attemptTooltip(attempts){
            return "Неудачных входов: " + attempts;
        },
        selectAll: {
            get: function () {
                return this.type.wallets ? this.selected.length == this.type.wallets.length : false;
            },
            set: function (value) {
                let selected = [];

                if (value) {
                    this.type.wallets.forEach(function (wallet) {
                        selected.push(wallet);
                    });
                }

                this.selected = selected;
            }
        },
        firstDayOfTheMonth()
        {
            const today = new Date();
            let mm = today.getMonth() + 1; //January is 0!
            const yyyy = today.getFullYear();
            if (mm < 10) {
                mm = '0' + mm;
            }

            return "01." + mm + "." + yyyy;
        },
    }
}