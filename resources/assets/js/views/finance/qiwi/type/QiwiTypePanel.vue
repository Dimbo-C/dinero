<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" v-text="type.name"></h3>
        </div>

        <div class="panel-body">
            <div class="responsive-table">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th width="20">
                            <div class="checkbox m-none">
                                <label class="p-t-xs">
                                    <input type="checkbox" v-model="selectAll">
                                </label>
                            </div>
                        </th>
                        <th class="tr-remove-padding">
                            <div v-on:click="setSortField('name')" class="table-header-block ">Имя</div>
                        </th>
                        <th class="tr-remove-padding">
                            <div v-on:click="setSortField('number')" class="table-header-block">Номер кошелька</div>
                        </th>
                        <th class="tr-remove-padding">
                            <div v-on:click="setSortField('visa')" class="table-header-block">Карта Visa Virtual</div>
                        </th>
                        <th class="tr-remove-padding">
                            <div v-on:click="setSortField('balance')" class="table-header-block">Баланс</div>
                        </th>
                        <th class="tr-remove-padding">
                            <div v-on:click="setSortField('income')" class="table-header-block">
                                Принятые средства с <span v-text="this.firstDayOfTheMonth"></span>
                            </div>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="w in type.wallets" v-if="w.is_active || isInactive">
                        <td>
                            <div class="checkbox m-none">
                                <label class="p -t-xs">
                                    <input type="checkbox" v-model="selected" :value="w">
                                </label>
                            </div>
                        </td>
                        <td v-text="w.name"></td>
                        <td v-text="w.login"></td>
                        <td v-text="w.settings.autoWithdrawal_card_number"></td>
                        <td>
                            <a data-toggle="tooltip"
                               data-placement="top"
                               title="Автовывод">
                                <i class="fa fa-upload fa-fw"
                                   v-bind:class="{'withdrawing-active':withdrawers.includes(w.login)}"
                                   v-bind:id="w.login"
                                   v-on:click.stop="autoWithdrawWallet(w.login)"></i>
                            </a>
                            <a data-toggle="tooltip"
                               data-placement="top"
                               title="Обновить">
                                <i class="fa fa-refresh fa-fw"
                                   v-bind:class="{'fa-spin':spinners.includes(w.login)}"
                                   v-bind:id="w.login"
                                   v-on:click.stop="updateWallet(w.login)"></i>
                            </a>
                            <a data-toggle="tooltip"
                               data-placement="top"
                               data-content="SWAG"
                               :login="w.login"
                               :title="attemptPopup(w.settings.failed_attempts)">
                                <i class="fa fa-square"
                                   :class="attemptClass(w.settings.failed_attempts)"
                                   aria-hidden="true"></i>
                            </a>
                            <span :id="w.login">
                                {{ spinnerSwitcher(w.balance, w.login)}}
                            </span>
                        </td>
                        <td>
                            <span>{{ tidySum(w.month_income) | currency}}</span>
                        </td>
                        <td class="text-right">
                            <div class="btn-group" role="group">
                                <router-link :to="'/finance/qiwi/' + w.login + '/withdraw'"
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="Ручной вывод">
                                    <i class="fa fa-usd"></i>
                                </router-link>
                                <router-link :to="'/finance/qiwi/' + w.login + '/history'"
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="История">
                                    <i class="fa fa-history"></i>
                                </router-link>
                                <router-link :to="'/finance/qiwi/' + w.login + '/settings'"
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="Настройки">
                                    <i class="fa fa-cog"></i>
                                </router-link>
                                <router-link :to="'/finance/qiwi/remove/' + w.login "
                                             class="btn btn-default"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="Удалить кошелек">
                                    <i class="fa fa-times"></i>
                                </router-link>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script src="./QiwiTypePanel.js"></script>
<style src="./QiwiTypePanel.css" scoped></style>