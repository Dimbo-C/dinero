<template>
    <div>
        <page-header icon="fa-bar-chart" title="Статистика">
            <router-link tag="li" to="/admins/own">
                <a>Администраторы системы</a>
            </router-link>
            <li>
                <span>OWN</span>
            </li>
        </page-header>

        <loading :show="loading"></loading>

        <transition name="fade">
            <div v-if="admins" class="container-fluid">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="panel panel-default panel-flush">
                            <div class="panel-heading">Персонал</div>
                            <div class="panel-body">
                                <ul role="tablist" class="nav nav-stacked">
                                    <li v-for="admin in admins">
                                        <!--<router-link :to="'/admins/own/stats/' + admin.id">-->
                                            <!--{{ admin.name }}-->
                                        <!--</router-link>-->
                                        <router-link :to="'/admins/own/metrics/'">
                                            {{ admin.name }}
                                        </router-link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-9">
                        <!-- Current Month Gross Turnover Chart -->
                        <div class="row" v-show="lastMonthsIndicators.length > 0">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Текущий месяц</div>

                                    <div class="panel-body">
                                        <canvas id="lastMonthGrossTurnover" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Previously Month Gross Turnover Chart -->
                        <div class="row" v-show="previousMonthsIndicators.length > 0">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Предыдущий месяц</div>

                                    <div class="panel-body">
                                        <canvas id="previousMonthGrossTurnover" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-primary panel-flush">
                                    <div class="panel-heading">
                                        <i class="fa fa-bar-chart fa-btn"></i>Сводка
                                    </div>

                                    <ul class="list-group">
                                        <li class="list-group-item">Общее за текущий месяц: {{ totalCurrentMonth | currency }}</li>
                                        <li class="list-group-item">Общее за прошлый месяц: {{ totalPreviouslyMonth | currency }}</li>
                                        <li class="list-group-item">Разница: {{ difference | currency }}</li>
                                        <li class="list-group-item">Результат:</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    import Vue from 'vue';
    import moment from 'moment';

    export default {

      /**
       * The component's data.
       */
      data() {
        return {
          loading: false,
          admins: null,
          monthlyGrossTurnover: 0,
          lastMonthsIndicators: [],
          previousMonthsIndicators: [],
        };
      },

      created () {
        this.addChartScript();
        this.fetchAdmins();
        this.getGrossIndicators();
      },

      watch: {
        '$route': 'fetchAdmins'
      },

      methods: {
        fetchAdmins () {
          this.loading = true;
          axios.get('/get-own-admins')
            .then((response) => {
              this.loading = false;
              this.admins = response.data;
            })
        },


        addChartScript () {
          let script = document.createElement('script');
          script.type = 'text/javascript';
          script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js';
          document.getElementsByTagName('head')[0].appendChild(script);
        },


        /**
         * Get the performance indicators for the application.
         */
        getGrossIndicators() {
          axios.get('/gross-indicators')
            .then(response => {
              this.lastMonthsIndicators = response.data.last_month;
              this.previousMonthsIndicators = response.data.previous_month;

              Vue.nextTick(() => {
                this.drawCharts();
              });
            });
        },


        /**
         * Draw the performance indicator charts.
         */
        drawCharts() {
          this.drawMonthlyGrossTurnoverChart('lastMonthGrossTurnover', this.lastMonthsIndicators);
          this.drawMonthlyGrossTurnoverChart('previousMonthGrossTurnover', this.previousMonthsIndicators);
        },


        /**
         * Draw the monthly recurring revenue chart.
         */
        drawMonthlyGrossTurnoverChart(id, indicators) {
          return this.drawCurrencyChart(
            id, indicators, 30, indicator => indicator.monthly_gross_turnover
          );
        },

        /**
         * Draw a chart with currency formatting on the Y-Axis.
         */
        drawCurrencyChart(id, indicators, days, dataGatherer) {
          return this.drawChart(id, indicators, days, dataGatherer, value => value.value);
        },

        /**
         * Draw a chart with the given parameters.
         */
        drawChart(id, indicators, days, dataGatherer, scaleLabelFormatter) {
          var dataset = this.baseChartDataSet;

          dataset.data = _.map(indicators, dataGatherer);

          // Here we will build out the dataset for the chart. This will contain the dates and data
          // points for the chart. Each chart on the Kiosk only gets one dataset so we only need
          // to add it a single element to this array here. But, charts could have more later.
          var data = {
            labels: this.availableChartDates(indicators),
            datasets: [dataset]
          };

          var options = { responsive: true };

          // If a scale label formatter was passed, we will hand that to this chart library to fill
          // out the Y-Axis labels. This is particularly useful when we want to format them as a
          // currency as we do on all of our revenue charts that we display on the Kiosk here.
          if (arguments.length === 4) {
            options.scaleLabel = scaleLabelFormatter;
          }

          var chart = new Chart(document.getElementById(id).getContext('2d'), {
            type: 'line',
            data: data,
            options: options
          });
        },


        /**
         * Get the available, formatted chart dates for the current indicators.
         */
        availableChartDates(indicators) {
          return _.map(indicators, indicator => moment(indicator.created_at).format('D.MM'));
        },
      },

      computed: {
        /**
         * Get the base chart data set.
         */
        baseChartDataSet() {
          return {
            label: "Валовой оборот",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
          };
        },

        totalPreviouslyMonth() {
          let sum = 0;

          this.previousMonthsIndicators.forEach((indicator) => {
            sum =+ indicator.monthly_gross_turnover;
          });

          return sum;
        },

        totalCurrentMonth() {
          let sum = 0;

          this.lastMonthsIndicators.forEach((indicator) => {
            sum =+ indicator.monthly_gross_turnover;
          });

          return sum;
        },

        difference() {
          return this.totalPreviouslyMonth - this.totalCurrentMonth;
        }
      },
    };
</script>