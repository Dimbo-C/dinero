<template>
    <modal id="modal-send-bitcoin" title="Отправить bitcoin">
        <template slot="modal-body">
            <div class="form form-horizontal">
                <div class="form-group">
                    <label class="control-label col-sm-4">Доступно:</label>

                    <div class="col-sm-8">
                        <p class="form-control-static">0,006021317843424 BTC</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount" class="control-label col-sm-4">Сумма перевода</label>

                    <div class="col-sm-8">
                        <input id="amount" type="text" class="form-control" v-model="form.amount">
                    </div>
                </div>

                <div class="form-group">
                    <label for="bitcoin-wallet-address" class="control-label col-sm-4">Адрес кошелька</label>

                    <div class="col-sm-8">
                        <input id="bitcoin-wallet-address" type="text" class="form-control" v-model="form.wallet">
                    </div>
                </div>
            </div>
        </template>

        <div class="modal-footer" slot="modal-footer">
            <button type="button" class="btn btn-primary">Отправить</button>
        </div>
    </modal>
</template>

<script>
  export default {
       /*
         * The component's data.
         */
        data() {
            return {
                form: new Form({
                  amount: '',
                  wallet: '',
                })
            };
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
                //
            },
        }
    }
</script>