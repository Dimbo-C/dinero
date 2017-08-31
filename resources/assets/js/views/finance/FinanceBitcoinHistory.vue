<template>
    <div>
        <page-header icon="fa-money" title="История транзакций Bitcoin">
            <li>
                <span>Финансы</span>
            </li>
            <router-link tag="li" to="/finance/bitcoin">
                <a>Bitcoin</a>
            </router-link>
        </page-header>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Сумма</th>
                                <th>Адрес кошелька</th>
                                <th>Ссылка на запись в блокчейне</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
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
                //
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