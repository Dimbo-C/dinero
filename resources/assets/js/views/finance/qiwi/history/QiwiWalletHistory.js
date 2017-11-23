import _sum from 'lodash/sum';
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
//                        days: [1, 3, 5, 0]
                    customPredictor: date => {
                        const dateStart = this.$data.state.dateStart;
                        const dateEnd = this.$data.state.dateEnd;

                        const startMs = (new Date(dateStart)).getTime();
                        const endMs = (new Date(dateEnd)).getTime();
                        const argMs = (new Date(date)).getTime();

                        if (argMs >= startMs && argMs <= endMs) {
                            return true;
                        }
                    }
                },
            },
            isLoaded: false,
            transactions: [],
            income: 0,
            expenditure: 0,
            dateRange: {
                start: '',
                end: '',
                page: 1,
                sizes: [20, 50, 100],
                size: 20
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
        listSizeChanged() {
            console.log("List size now", this.dateRange.size);
            this.fetchReport(1);
        },
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
                    })
                    .catch(() => {
                        Bus.$emit(
                                'showNotification',
                                "danger",
                                "Не удалось загрузить историю транзакций, попробуйте позже");
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
