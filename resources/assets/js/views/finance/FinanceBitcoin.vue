<template>
    <div>
        <page-header icon="fa-money" title="Bitcoin">
            <router-link tag="li" to="/finance">
                <a>Финансы</a>
            </router-link>
        </page-header>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p><strong>Баланс:</strong>
                                0.006021317843424 BTC ({{ 1000 | currency('RUB') }})</p>
                            <p class="small">Данная конвертация примерная,
                                разница в курсах может быть значительной от бирже к бирже</p>
                        </div>

                        <div class="panel-footer">
                            <button class="btn btn-default"
                                    @click="showModal('modal-take-bitcoin')">Принять</button>
                            <button class="btn btn-default"
                                    @click="showModal('modal-send-bitcoin')">Отправить</button>
                            <router-link to="/finance/bitcoin/history" class="btn btn-default">
                                История транзакций
                            </router-link>
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
  };
</script>