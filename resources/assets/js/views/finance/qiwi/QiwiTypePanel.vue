<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" v-text="type.name"></h3>
        </div>

        <div class="panel-body">
            <div class="responsive-table">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th width="20">
                            <div class="checkbox m-none">
                                <label class="p-t-xs">
                                    <input type="checkbox" v-model="selectAll">
                                </label>
                            </div>
                        </th>
                        <th>Имя кошелька </th>
                        <th>Номер кошелька</th>
                        <th>Карта Visa Virtual</th>
                        <th>Баланс</th>
                        <th> Принятые средства с <span v-text="this.firstDayOfTheMonth"></span></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="w in type.wallets" v-if="w.is_active || isInactive">
                        <td>
                            <div class="checkbox m-none">
                                <label class="p -t-xs">
                                    <input type="checkbox" v-model="selected" :value="w">
                                </label>
                            </div>
                        </td>
                        <td v-text="w.name"></td>
                        <td v-text="w.login"></td>
                        <td v-text="w.settings.autoWithdrawal_card_number"></td>
                        <td v-if="!isInactive">
                            <a data-toggle="tooltip"
                               data-placement="top"
                               title="Автовывод">
                                <i class="fa fa-upload fa-fw"
                                   v-bind:class="{'withdrawing-active':withdrawers.includes(w.login)}"
                                   v-bind:id="w.login"
                                   v-on:click.stop="autoWithdrawWallet(w.login)"></i>
                            </a>
                            <span :id="w.login">
                                {{ moneys(w.balance, w.login)}}
                            </span>
                            <a data-toggle="tooltip"
                               data-placement="top"
                               title="Обновить">
                                <i class="fa fa-refresh fa-fw"
                                   v-bind:class="{'fa-spin':spinners.includes(w.login)}"
                                   v-bind:id="w.login"
                                   v-on:click.stop="updateWallet(w.login)"></i>
                            </a>
                        </td>
                        <td v-if="!isInactive">
                            <span>{{ tidySum(w.month_income) | currency }}</span>
                        </td>
                        <td class="text-right">
                            <div class="btn-group" role="group">
                                <router-link :to="'/finance/qiwi/' + w.login + '/withdraw'"
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="Ручной вывод">
                                    <i class="fa fa-usd"></i>
                                </router-link>
                                <router-link :to="'/finance/qiwi/' + w.login + '/history'"
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="История">
                                    <i class="fa fa-history"></i>
                                </router-link>
                                <router-link :to="'/finance/qiwi/' + w.login + '/settings'"
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="Настройки">
                                    <i class="fa fa-cog"></i>
                                </router-link>
                                <router-link :to="'/finance/qiwi/remove/' + w.login "
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="Удалить кошелек">
                                    <i class="fa fa-times"></i>
                                </router-link>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!--<div class="panel-footer">-->
        <!--<div class="form-inline">-->
        <!--<label for="" class="control-label">Перенести отмеченные в:</label>-->
        <!--<div class="form-group m-b-none">-->
        <!--<select name="" id="" class="form-control" v-model="moveTo">-->
        <!--<option v-for="t in types"-->
        <!--v-if="t.id !== type.id && t.slug !== exclude"-->
        <!--:value="t.id">-->
        <!--{{ t.name }}-->
        <!--</option>-->
        <!--</select>-->
        <!--</div>-->

        <!--<div class="form-group">-->
        <!--<button class="btn btn-default"-->
        <!--@click="moveWallets()"-->
        <!--:disabled="!selected.length">-->
        <!--Выполнить-->
        <!--</button>-->
        <!--</div>-->
        <!--</div>-->
        <!--<div class="clearfix"></div>-->
        <!--</div>-->
    </div>
</template>

<script>
    import Table from '../../../mixins/table';

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
                withdrawers: []
            };
        },

        mounted() {
            this.items = this.type.wallets;
        },

        watch: {
            selected(val) {
                this.$emit('updateSelected', val);
            },
        },

        methods: {
            moneys(balance, login) {
                if (this.spinners.includes(login)) {
                    return "...";
                } else {
                    return this.tidySum(balance) + " " + Dinero.currencySymbol;
                }
            },
            moveWallets() {
                console.log(this.selected);
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
                        console.log(error.response);
                        this.updateBalanceCallback(login);
                    });
            },

            updateBalanceCallback(login, balance = null) {
                this.items.map((item) => {
                    if (item.login === login) {
                        if (balance !== null) {
                            item.balance = this.tidySum(balance);
                        }
                        this.spinners = this.spinners.filter((elem) => login !== elem);
                    }
                });
            },

            updateIncome(login) {
                let auth = {"login": login};
                Dinero.post('/api/qiwi-wallets/update-income', new Form(auth))
                    .then((response) => {
//                        const income = response.monthIncome;
//                        console.log("Income: " + income);
//                        this.items.map((item) => {
//                            if (item.login === login) {
//                                item.month_income = this.tidySum(income);
//                            }
//                        });
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
//                        this.updateWallet(login);
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

            tidySum(sum) {
                let str = (typeof sum === "object") ? "0.00" : (sum + "");
                if (str === "") str = "0.00";

                str = str.replace(/,/g, "");
                str = parseFloat(str).toFixed(2);

                return str;
            }
        },
        computed: {
            firstDayOfTheMonth() {
                const today = new Date();
                let mm = today.getMonth() + 1; //January is 0!
                const yyyy = today.getFullYear();
                if (mm < 10) {
                    mm = '0' + mm;
                }

                return "01." + mm + "." + yyyy;
            },
        }
    };
</script>

<style scoped>
    .withdrawing-active {
        background-color: #fff;
        color: green;
    }
</style>