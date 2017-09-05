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
                        <th v-if="!isInactive">Баланс</th>
                        <th v-if="!isInactive">Принятые средства с <span v-text="this.firstDayOfTheMonth"></span></th>

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
                        <td v-if="!isInactive">
                            <span :id="w.login">{{ w.balance | currency }}</span>
                            <a data-toggle="tooltip" data-placement="top" title="Обновить"
                               v-on:click.stop="updateWallet(w.login,w.password)">
                                <i class="fa fa-refresh fa-fw"></i>
                            </a>
                        </td>
                        <td v-if="!isInactive">
                            <span>{{ w.month_income | currency }}</span>
                            <a href="#" data-toggle="tooltip" data-placement="top" title="Вывести">
                                <i class="fa fa-sign-out fa-fw"></i>
                            </a>
                        </td>
                        <td class="text-right">
                            <div class="btn-group" role="group">
                                <button type="button"
                                        class="btn btn-default"
                                        data-toggle="tooltip"
                                        data-placement="top" title="Ручной вывод"
                                >
                                    <i class="fa fa-usd"></i>
                                </button>
                                <router-link :to="'/finance/qiwi/' + w.login + '/history'"
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="История"
                                >
                                    <i class="fa fa-history"></i>
                                </router-link>
                                <router-link :to="'/finance/qiwi/' + w.login + '/settings'"
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="Настройки"
                                >
                                    <i class="fa fa-cog"></i>
                                </router-link>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel-footer">
            <div class="form-inline">
                <label for="" class="control-label">Перенести отмеченные в:</label>
                <div class="form-group m-b-none">
                    <select name="" id="" class="form-control" v-model="moveTo">
                        <option v-for="t in types"
                                v-if="t.id !== type.id && t.slug !== exclude"
                                :value="t.id"
                        >
                            {{ t.name }}
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <button class="btn btn-default" @click="moveWallets()" :disabled="!selected.length">Выполнить
                    </button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</template>

<script>
    import Table from '../../../mixins/table';

    export default {
        mixins: [Table],
        props: ['type', 'types', 'exclude', 'is-inactive'],
        data () {
            return {
                moveTo: this.types.filter(t => t.id !== this.type.id)[0].id,
                foo: ''
            };
        },
        mounted () {
            this.items = this.type.wallets;
        },
        methods: {
            moveWallets () {
                const moveFrom = this.isInactive ? this.selected[0].type_id : this.type.id;
                this.$emit('moveWallets', this.selected, moveFrom, this.moveTo)
            },
            updateWallet(login, password) {

                let auth = {"login": login, "password": password};
                Dinero.post('/api/qiwi-wallets/update', new Form(auth))
                    .then((data) => {
                            console.log(data);
                            this.items.map((item) => {
                                if (item.login === login) {
                                    item.balance = data.balance;
                                    item.month_income = data.month_income;
                                }
                            });
                        }
                    )
            }
        },
        computed: {
            firstDayOfTheMonth () {
                var today = new Date();
                var mm = today.getMonth() + 1; //January is 0!
                var yyyy = today.getFullYear();
                if (mm < 10) {
                    mm = '0' + mm;
                }

                return "01." + mm + "." + yyyy;
            },
        }
    };
</script>