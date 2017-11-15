<template>
    <div>
        <page-header icon="fa-money" title="Финансы"></page-header>

        <div class="container-fluid">
            <div class="m-b-lg">
                <div class="btn-group p-b-xs">
                    <router-link to="/finance/rent" class="btn btn-success">
                        Счета аренды
                    </router-link>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default panel-flush">
                        <div class="panel-heading">
                            <ul class="nav nav-pills nav-justified" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#bitcoin" aria-controls="home" role="tab" data-toggle="tab">BitCoin</a>
                                </li>
                                <li role="presentation">
                                    <a href="#qiwi" aria-controls="profile" role="tab" data-toggle="tab">Qiwi Wallet</a>
                                </li>
                            </ul>
                        </div>

                        <div class="panel-body tab-content">
                            <div role="tabpanel" class="tab-pane active" id="bitcoin">
                                <div class="p-md">
                                    <p><strong>Баланс:</strong>
                                        0.006021317843424 BTC ({{ 1000 | currency('RUB') }})</p>
                                    <p class="small">Данная конвертация примерная,
                                        разница в курсах может быть значительной от бирже к бирже</p>
                                    <button class="btn btn-default"
                                            @click="showModal('modal-take-bitcoin')">Принять
                                    </button>
                                    <button class="btn btn-default"
                                            @click="showModal('modal-send-bitcoin')">Отправить
                                    </button>
                                    <router-link to="/finance/history/bitcoin" class="btn btn-default">
                                        История транзакций
                                    </router-link>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="qiwi">
                                <div class="p-md">
                                    <p>
                                        <button class="btn btn-default">Панель управления</button>
                                        <button class="btn btn-default">Персональный просмотр</button>
                                    </p>

                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Финасовая информация:</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Оборот за месяц ({{currencySymbol}}):</td>
                                        </tr>
                                        <tr>
                                            <td>Оборот за сегодня:</td>
                                        </tr>
                                        <tr>
                                            <td>Оборот за вчера:</td>
                                        </tr>
                                        <tr>
                                            <td>Доступные к выводу средства (на данный момент):</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <take-bitcoin-modal></take-bitcoin-modal>
        <send-bitcoin-modal></send-bitcoin-modal>
    </div>
</template>

<script>
    import TakeBitcoinModal from './TakeBitCoinModal.vue';
    import SendBitcoinModal from './SendBitcoinModal.vue';

    export default {
        components: {
            TakeBitcoinModal,
            SendBitcoinModal
        },
        methods: {
            showModal(id) {
                $(`#${id}`).modal('show');
            }
        },
        data() {
            return {
                currencySymbol: Dinero.currencySymbol
            }

        }
    };
</script>