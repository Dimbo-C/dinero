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
                    <ul class="list-inline">
                        <li><a href="javascript:;" @click="setDateRange('today')">За сегодня</a></li>
                        <li>&bull;</li>
                        <li><a href="javascript:;" @click="setDateRange('yesterday')">За вчера</a></li>
                        <li>&bull;</li>
                        <li><a href="javascript:;" @click="setDateRange('month')">За текущий месяц</a></li>
                    </ul>

                    <div class="form-group col-lg-6 col-xs-12">
                        <label for="sel1">Отображать записей:</label>
                        <select @change="listSizeChanged()"
                                v-model="dateRange.size"
                                class="form-control"
                                id="sel1">
                            <option v-for="size in dateRange.sizes">{{size}}</option>
                        </select>
                    </div>
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
                                        :disabled="this.transactions.length<this.dateRange.size"
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

<script src="./QiwiWalletHistory.js"></script>
<style src="./QiwiWalletHistory.css" scoped></style>
