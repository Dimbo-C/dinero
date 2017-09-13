<template>
    <div>
        <page-header icon="fa-money" title="История транзакций">
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
            <div class="row m-b-lg">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="" class="control-label">Показать записи по указанным датам:</label>

                        <div class="input-group input-group-sm">
                            <span class="input-group-addon">с</span>
                            <masked-input
                                    class="form-control"
                                    v-model="dateRange.start"
                                    mask="99.99.9999"
                            ></masked-input>
                            <span class="input-group-addon">по</span>
                            <masked-input
                                    class="form-control"
                                    v-model="dateRange.end"
                                    mask="99.99.9999"
                            ></masked-input>
                        </div>
                    </div>
                    <ul class="list-inline">
                        <li><a href="javascript:;" @click="setDateRange('today')">За сегодня</a></li>
                        <li>&bull;</li>
                        <li><a href="javascript:;" @click="setDateRange('yesterday')">За вчера</a></li>
                        <li>&bull;</li>
                        <li><a href="javascript:;" @click="setDateRange('month')">За текущий месяц</a></li>
                    </ul>
                </div>
            </div>

            <hr>

            <loading :show="!isLoaded"></loading>

            <div v-if="isLoaded" class="panel panel-default">
                <div class="panel-heading">
                    <div class="form-inline">
                        <div class="pull-left">
                            <div class="form-group">
                                <p class="form-control-static">История транзакций <strong v-text="this.login"></strong>
                                    (+ {{income}} / - {{outcome}})</p>
                            </div>
                        </div>
                        <div class="pull-right">
                            <label class="control-label" for="">Поиск:</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="panel-body">
                    <div v-if="!transactions.length">Нет отчетов за указанный период</div>
                    <div v-else class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Дата и время</th>
                                <th>Код</th>
                                <th>Статус</th>
                                <th>Реквизит</th>
                                <th>Коментарий*</th>
                                <th>Сумма</th>
                                <th>Комиссия</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="t in transactions">
                                <td>
                                    <p class="small m-b-none" v-text="t.date"></p>
                                    <p class="small m-b-none" v-text="t.time"></p>
                                </td>
                                <td v-text="t.transaction"></td>
                                <td v-html="status(t)"></td>
                                <td>
                                    <p class="small m-b-none" v-text="t.provider"></p>
                                    <p class="small m-b-none" v-text="t.opNumber"></p>
                                </td>
                                <td v-html="comment(t)"></td>
                                <td>{{ t.sign }}{{ t.amount }} {{ t.currency }}</td>
                                <td v-text="t.commission"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import _sum from 'lodash/sum';

    export default {
        data() {
            return {
                isLoaded: false,
                transactions: null,
                income: 0,
                outcome: 0,
                dateRange: {
                    start: '',
                    end: '',
                }
            };
        },

        watch: {
            dateRange: {
                handler (val) {
                    if (val.start !== '' && val.end !== '') {
                        this.fetchReport();
                    }
                },
                deep: true,
            },
        },

        /**
         * Prepare the component.
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            fetchReport() {
                this.isLoaded = false;
                axios.get(`/api/qiwi-wallets/${this.login}/report`, {params: this.dateRange})
                    .then((response) => {
                        console.log(response);
                        this.transactions = response.data.history;
                        this.income = response.data.income;
                        this.outcome = response.data.outcome;
                        this.isLoaded = true;
                    })
            },

            customFormatter (date) {
                return moment(date).format('dd.MM.yyyy')
            },

            setDateRange (key) {
                if (key === 'today') {
                    this.dateRange.start = this.dateRange.end = moment().format('L')
                } else if (key === 'yesterday') {
                    this.dateRange.start = moment().subtract(1, 'days').format('L');
                    this.dateRange.end = moment().format('L')
                } else if (key === 'month') {
                    this.dateRange.start = moment().startOf('month').format('L');
                    this.dateRange.end = moment().format('L')
                }
            },

            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.setDateRange('today');

                this.$nextTick(() => {
                    $('.tooltip').removeClass('in');
                });
            },

            comment(t){
                if (t.status === 'error') {
                    return t.comment + '<p style="color:red"> (' + t.errorMessage + ')</p>'
                } else {
                    return t.comment;
                }
            },

            status (t) {
                if (t.status === 'error') {
                    return '<i class="fa fa-circle text-danger"></i> Ошибка'
                } else if (t.status === 'error') {
                    return '<i class="fa fa-circle text-warning"></i> В обработке'
                } else {
                    return '<i class="fa fa-circle text-success"></i> Успешно'
                }
            }
        },

        computed: {
            login () {
                return this.$route.params.wallet;
            },

//            income () {
//                if (this.transactions) {
////                    let amounts = [];
////                    let sum = 0;
////                    const transactions = this.transactions.filter(t => t.amount_sign === '-');
////
////                    transactions.forEach((t) => {
////                        amounts.push(parseFloat(t.amount.replace(',', '.').replace(/[^0-9]/, '')))
////                    });
////
////                    return amounts;
//                }
//            },

            expenditure () {
//                if (this.transactions) {
//                    return `${_sum(this.transactions.filter(t => t.amount_sign === '+').amount)}`;
//                }
            }
        }
    };
</script>