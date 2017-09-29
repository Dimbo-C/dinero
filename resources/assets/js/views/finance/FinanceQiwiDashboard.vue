<template>
    <div>
        <page-header icon="fa-money" title="Панель управления">
            <li>
                <a class="disabled">Финансы</a>
            </li>
            <router-link tag="li" to="/finance/qiwi">
                <a>Qiwi Visa Wallet</a>
            </router-link>
        </page-header>

        <loading :show="!walletsIsLoaded"></loading>

        <div v-if="walletsIsLoaded" class="container-fluid">

            <div class="form-inline">
                <h3 class="body-header">Управление кошельками</h3>

                <div class="form-group m-b-none">
                    <select name="" id="" class="form-control"
                            v-model="massActionValue">
                        <option v-for="(text, action) in actions"
                                :value=action>
                            {{ text }}
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <button class="btn btn-default"
                            @click="massAction">
                        Выполнить
                    </button>
                </div>
            </div>

            <br>
            <div class="m-b-lg">
                <router-link to="/finance/qiwi/add-wallet" class="btn btn-success">
                    <i class="fa fa-plus-square fa-btn"></i>Добавить кошелёк
                </router-link>
            </div>

            <div class="input-group m-b-lg">
                <input type="text" placeholder="" class="form-control" v-model="searchQuery">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default">Поиск</button>
                </span>
            </div>


            <qiwi-type-panel v-for="type in walletsTypes"
                             :key="type.id"
                             :type="type"
                             :types="walletsTypes"
                             @moveWallets="moveWallets"
            ></qiwi-type-panel>

            <qiwi-type-panel :type="inactive"
                             :types="walletsTypes"
                             :exclude="'spent'"
                             @func="func"
                             :is-inactive="true"
                             @moveWallets="moveWallets">
            </qiwi-type-panel>
        </div>
    </div>
</template>

<script>
    import Table from '../../mixins/table';
    import QiwiTypePanel from './qiwi/QiwiTypePanel.vue';

    export default {
        components: {QiwiTypePanel},
        mounted() {
            this.fetchWallets();
        },
        data() {
            return {
                actions: {
                    moveToReceive: "Переместить в приемные",
                    moveToWithdraw: "Переместить в автовыводные",
                    moveToReserve: "Переместить в резервные",
                    moveToSpent: "Переместить в отработанные",
                    remove: "Удалить"
                },
                massActionValue: "",

                searchQuery: '',
                walletsIsLoaded: false,
                walletsTypes: null,

                hui: []
            };
        },
        watch: {
            filter () {

            }
        },
        methods: {

            func(arg){
                console.log(arg);

            },
            fetchWallets () {
                axios.get('/api/qiwi-wallets')
                    .then((response) => {
                        this.walletsTypes = response.data;
                        this.walletsIsLoaded = true;

                        Bus.$emit('initTooltip');
                    })
            },

            moveWallets (wallets, fromId, toId) {
                // convert array of wallets entities to array ids
                let ids = wallets.map((wallet) => wallet.id);
                axios.post('/api/qiwi-wallets/move', {wallets: ids, to: toId})
                    .then(() => {
                        let moveTo = this.walletsTypes.find(type => type.id === toId);

                        let moveFrom = this.walletsTypes.find(type => type.id === fromId);

                        moveFrom.wallets = moveFrom.wallets.filter((w) => {
                            return !wallets.find(item => item.id === w.id);
                        });

                        wallets.forEach((w) => {
                            w.is_active = 1;
                            moveTo.wallets.push(w)
                        });
                    });
            },

            massAction () {
                console.log(this.massActionValue);
                console.log(this.$children);
                console.log(this.$children[0].$attrs);
            },
            removeFromType (wallets, fromId) {

            }
        },
        computed: {
            inactive () {
                let inactive = {name: 'Неактивные киви', wallets: [], selected: []};

                this.walletsTypes.forEach((type) => {
                    type.wallets.forEach((w) => {
                        if (!w.is_active) {
                            inactive.wallets.push(w);
                        }
                    });
                });

                return inactive;
            },
        },
    };
</script>