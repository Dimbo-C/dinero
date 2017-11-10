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
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="" class="control-label">Показать записи по указанным датам:</label>

                        <div class="input-group">
                            <datepicker
                                    :language="state.language"
                                    :input-class="state.inputClass"
                                    :calendar-class="state.calendarClass"
                                    :wrapper-class="state.wrapperClass"
                                    :highlighted="state.highlighted"
                                    :format="customFormatter"
                                    :bootstrapStyling="state.bootstrapStyling"
                                    :full-month-name="state.fullMonthName"
                                    :monday-first="state.mondayFirst"

                                    :value="state.dateStart"
                                    v-model="state.dateStart"></datepicker>

                            <span class="input-group-addon">по</span>

                            <datepicker
                                    :language="state.language"
                                    :input-class="state.inputClass"
                                    :calendar-class="state.calendarClass"
                                    :wrapper-class="state.wrapperClass"
                                    :highlighted="state.highlighted"
                                    :format="customFormatter"
                                    :bootstrapStyling="state.bootstrapStyling"
                                    :full-month-name="state.fullMonthName"
                                    :monday-first="state.mondayFirst"

                                    :value="state.dateEnd"
                                    v-model="state.dateEnd"></datepicker>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12 col-lg-6 col-lg-offset-3">
                                <button class="btn btn-success col-xs-6 col-xs-offset-3 col-lg-6" @click="update">
                                    Обновить
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--<div class="form-group">-->
                    <!--<label for="" class="control-label">Показать записи по указанным датам:</label>-->

                    <!--<div class="input-group input-group-sm">-->
                    <!--<span class="input-group-addon">с</span>-->
                    <!--<masked-input-->
                    <!--class="form-control"-->
                    <!--v-model="dateRange.start"-->
                    <!--mask="99.99.9999"-->
                    <!--&gt;</masked-input>-->
                    <!--<span class="input-group-addon">по</span>-->
                    <!--<masked-input-->
                    <!--class="form-control"-->
                    <!--v-model="dateRange.end"-->
                    <!--mask="99.99.9999"-->
                    <!--&gt;</masked-input>-->
                    <!--</div>-->
                    <!--</div>-->
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
                                <p class="form-control-static">История транзакций <b v-text="this.login"></b>
                                    c <b>{{dateRange.start}}</b> по <b>{{dateRange.end}}</b>
                                    (+ {{income}} / - {{expenditure}})
                                </p>
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
                    <div v-if="!transactions.length && dateRange.page==1">Нет отчетов за указанный период</div>
                    <div v-else>
                        <div class="table-responsive">
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
                        <div class="row">
                            <div class="col-xs-4 col-md-2">
                                <button class="btn btn-primary"
                                        :disabled="this.dateRange.page==1"
                                        @click="prevPage">Предыдущая страница
                                </button>
                            </div>
                            <div class="col-xs-3">
                                <button class="btn btn-primary"
                                        :disabled="this.transactions.length<15"
                                        @click="nextPage">Следующая страница
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import _sum from 'lodash/sum';
    import _sumBy from 'lodash/sumBy';
    import Datepicker from 'vuejs-datepicker';

    export default {
        components: {datepicker: Datepicker},
        data() {
            return {
                state: {
                    dateStart: "",
                    dateEnd: "",
                    inputClass: "form-control input-group-addon",
                    calendarClass: "green",
                    wrapperClass: "wrapper-class",
                    language: "ru",
                    bootstrapStyling: false,
                    fullMonthName: true,
                    mondayFirst: true,
                    highlighted: {
                        days: [1, 3, 5, 0]
                    }
                },
                isLoaded: false,
                transactions: [],
                income: 0,
                expenditure: 0,
                dateRange: {
                    start: '',
                    end: '',
                    page: 1
                },
            };
        },


        watch: {
            dateRange: {
//                handler (val) {
//                    if (val.start !== '' && val.end !== '') {
//                        this.fetchReport();
//                    }
//                },
//                deep: true,
            },
        },

        /**
         * Prepare the component.
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            update() {
                this.fetchReport(1);
            },

            fetchReport(page) {
                let start = moment(this.state.dateStart).format("DD.MM.YYYY");
                let end = moment(this.state.dateEnd).format("DD.MM.YYYY");
                this.dateRange.page = page;
                this.fetchReportByDate(start, end);
            },
            fetchReportByDate(start, end) {
                this.dateRange.start = start;
                this.dateRange.end = end;
                this.isLoaded = false;

                // get history of transactions
                axios.get(`/api/qiwi-wallets/${this.login}/report`, {params: this.dateRange})
                    .then((response) => {
                        console.log(response);
                        this.transactions = response.data;
                        this.isLoaded = true;
                    });

                // get income and expenditure
                axios.get(`/api/qiwi-wallets/${this.login}/incomeExpenditure`, {params: this.dateRange})
                    .then((response) => {
                        console.log(response);
                        this.income = response.data.income;
                        this.expenditure = response.data.expenditure;
                    });
            },

            customFormatter(date) {
                return moment(date).format('DD.MM.YYYY')
            },

            setDateRange(key) {
                if (key === 'today') {
                    this.state.dateStart = moment().toDate();
                    let start = moment().format('DD.MM.YYYY');

                    this.fetchReportByDate(start, start);
                } else if (key === 'yesterday') {
                    let startMoment = moment().subtract(1, 'days');
                    let start = startMoment.format('L');
                    this.state.dateStart = startMoment.toDate();

                    let end = moment().format('L');
                    this.state.dateEnd = moment().toDate();

                    this.fetchReportByDate(start, end);
                } else if (key === 'month') {
                    let startMoment = moment().startOf("month");
                    let start = startMoment.format('L');
                    this.state.dateStart = startMoment.toDate();
                    let end = moment().format('L');
                    this.state.dateEnd = moment().toDate();

                    this.fetchReportByDate(start, end);
                }
            },

            nextPage() {
                const nextPageNumber = this.dateRange.page + 1;
                this.fetchReport(nextPageNumber);
            },
            prevPage() {
                const prevPageNumber = this.dateRange.page === 1
                    ? this.dateRange.page
                    : this.dateRange.page - 1;
                this.fetchReport(prevPageNumber);
            },

            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.state.dateStart = moment().toDate();
                this.state.dateEnd = moment().toDate();
                this.setDateRange('today');

                this.$nextTick(() => {
                    $('.tooltip').removeClass('in');
                });
            },

            comment(t) {
                if (t.status === 'error') {
                    return t.comment + '<p style="color:red"> (' + t.errorMessage + ')</p>'
                } else {
                    return t.comment;
                }
            },

            status(t) {
                if (t.status === 'error') {
                    return '<i class="fa fa-circle text-danger"></i> Ошибка'
                } else if (t.status === 'error') {
                    return '<i class="fa fa-circle text-warning"></i> В обработке'
                } else {
                    return '<i class="fa fa-circle text-success"></i> Успешно'
                }
            },

            // calculate income and expenditure from history list
            historySum(sign) {
                if (this.transactions) {
                    const transactions = this.transactions
                        .filter(t => t.sign === sign)
                        .map(t => t.amount = Number(t.amount));

                    return _sum(transactions).toFixed(2);
                }
            }
        },

        computed: {
            login() {
                return this.$route.params.wallet;
            },

//            income () {
//                return this.historySum("+");
//            },
//
//            expenditure () {
//                return this.historySum("-");
//            }

        }
    };
</script>

<style scoped>
    .cell.day {
        background-color: #000000;
    }

    .green {
        background-color: #6eff00;
    }
</style>
