<template>
    <div>
        <page-header icon="fa-money" title="Вывод средств">
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
        <div class="container-fluid"
             @keyenter="saveSettings">
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-12 col-md-8">
                                    Настройки кошелька Qiwi ({{ form.login}})
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <button class="btn btn-primary full-width"
                                            @click="showSetting('security')">Безопасность
                                    </button>
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <button class="btn btn-primary full-width marginless paddingless"
                                            @click="showSetting('identification')">Идентификация
                                    </button>
                                </div>
                            </div>
                        </div>


                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div v-if="alert.show"
                                     v-bind:class="[alert.className]"
                                     class="alert">
                                    {{alert.text}}
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Название кошелька</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.name">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Комментарий к кошельку</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control"
                                                  v-model="form.comments"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.useProxy">
                                                Использовать прокси
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="form.useProxy">
                                    <label for="" class="col-sm-4 control-label">Прокси сервер</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="proxyServer"
                                               placeholder="host:port">
                                    </div>
                                </div>

                                <div class="form-group" v-if="form.useProxy">
                                    <label for="" class="col-sm-4 control-label">Авторизация прокси</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               :disabled="!form.useProxy"
                                               v-model="proxyAuth"
                                               placeholder="login:password">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="form.walletActive"
                                                       checked>
                                                Кошелек активен
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="form.alwaysOnline"
                                                       checked>
                                                Режим «Всегда онлайн»
                                            </label>
                                        </div>
                                        <span class="help-block">Поставьте галочку,
                                            если хотите чтобы сессия кошелька всегда поддерживалась в режиме
                                            онлайн. Аналогично постоянному нахождению в браузере.
                                            Если галочка не стоит - сессия будет слетать и кошелек будет перелогиниваться.
                                            Не используйте для всех кошельков поголовно, только для автовыводных и приемных,
                                            где своевременное снятие денег в приоритете.
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Частота проверки баланса, мин.</label>
                                    <div class="col-sm-8">
                                        <input type="number" min="0"
                                               v-model="form.balanceRecheckTimeout"
                                               class="form-control">
                                        <span class="help-block">Укажите через какое количество минут система должна автоматически
                                        обновлять баланс кошелька. Чтобы отключить функцию введите 0</span>
                                    </div>
                                </div>

                                <!--<div class="form-group">-->
                                <!--<label for="" class="col-sm-4 control-label">Тип кошелька</label>-->
                                <!--<div class="col-sm-8">-->
                                <!--<input type="text" class="form-control">-->
                                <!--</div>-->
                                <!--</div>-->

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Тип кошелька</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" v-model="form.walletType">
                                            <option v-for="o in form.walletTypes" :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Максимальный баланс</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.maximumBalance">
                                        <span class="help-block">Максимальный баланс кошелька, при достижении которого
                                            кошелек автоматически уходит в резервные</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Минимальный баланс</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.minimumBalance">
                                        <span class="help-block">Минимальный баланс кошелька.
                                            Работает на автовыводе.
                                            Оставляет сумму на балансе кошелька.
                                            Для отключения введите 0.
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Баланс для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.autoWithdrawalMinBalance">
                                        <span class="help-block">Работает только если кошелек настроен на автовывод.
                                            Автовывод срабатывает если баланс кошелька больше либо равен указанной сумме.
                                            Для отключения введите 0.
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Лимит транзакций на вывод</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.autoWithdrawalLimit">
                                        <span class="help-block">Работает только если кошелек настроен на автовывод.
                                            За 1 раз будет выведена сумма, не превышающая данное значение.
                                            Для отключения введите 0.
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.autoWithdrawalActive">
                                                Автовывод включен
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Режим работы автовывода</label>
                                    <div class="col-sm-8">
                                        <select class="form-control"
                                                v-model="form.autoWithdrawalType">
                                            <option v-for="o in form.autoWithdrawalTypes"
                                                    :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Тип автовывода</label>
                                    <div class="col-sm-8">
                                        <select class="form-control"
                                                v-model="form.autoWithdrawalTarget">
                                            <option v-for="o in form.autoWithdrawalTargets"
                                                    :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for=""
                                           class="col-sm-4 control-label"
                                           v-model="form.autoWithdrawalType">
                                        Вызывать автовывод каждые X минут
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="form.autoWithdrawalTimeout">
                                        <span class="help-block">Работает только когда кошелек настроен на автовывод.
                                            Как только с момента последнего автовывода прошло указанное количество минут,
                                            вызывается автовывод, если режим автовывода указан <b>Каждые Х минут</b>.
                                            </span>
                                    </div>
                                </div>

                                <!--<div class="form-group">-->
                                <!--<label for="" class="col-sm-4 control-label">Минимальная сума для автовывода</label>-->
                                <!--<div class="col-sm-8">-->
                                <!--<input type="text"-->
                                <!--class="form-control"-->
                                <!--v-model="form.minimumAutoWithdrawAmount"-->
                                <!--:disabled="!form.autoWithdrawalActive">-->
                                <!--<span class="help-block">Минимальный баланс кошелька при котором должен быть совершен вывод</span>-->
                                <!--</div>-->
                                <!--</div>-->

                                <!--<div class="form-group">-->
                                <!--<div class="col-sm-offset-4 col-sm-8">-->
                                <!--<div class="checkbox">-->
                                <!--<label>-->
                                <!--<input type="radio" v-model="form.usingVouchers">-->
                                <!--Автовывод с помощью ваучеров-->
                                <!--</label>-->
                                <!--</div>-->
                                <!--</div>-->
                                <!--</div>-->

                                <!--<div class="form-group">-->
                                <!--<div class="col-sm-offset-4 col-sm-8">-->
                                <!--<div class="checkbox">-->
                                <!--<label>-->
                                <!--<input type="radio"-->
                                <!--value="wallet"-->
                                <!--v-model="form.withdrawTarget">-->
                                <!--Автовывод с помощью ваучеров-->
                                <!--</label>-->
                                <!--</div>-->
                                <!--</div>-->
                                <!--</div>-->

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Кошельки для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: +79123456789"
                                               v-model="autoWithdrawalWallets">
                                        <span class="help-block">Укажите множество кошельков через пробел, ';' или ",",
                                            на которые будет совершен автовывод.
                                            Если один из них достигнет максимального баланса
                                            - остаток денег будет переведен на следующий кошелек.
                                            </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Карта для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="cardNumber"
                                               placeholder="XXXX XXXX XXXX XXXX">
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Данные владельца карты</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawalCardholderName"
                                               placeholder="Имя">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawalCardholderSurname"
                                               placeholder="Фамилия">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-4 col-xs-offset-1 col-lg-2 col-lg-offset-4">
                                        <button class="btn btn-primary"
                                                @click="saveSettings">Сохранить
                                        </button>
                                    </div>

                                    <!--<div class="col-xs-4 col-xs-offset-2 col-lg-2 col-lg-offset-2">-->
                                    <!--<button class="btn btn-primary"-->
                                    <!--@click="triggerAutoWithdraw">-->
                                    <!--Автовывод-->
                                    <!--</button>-->
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script src="./QiwiWalletGeneralSettings.js"></script>
<style src="./QiwiWalletGeneralSettings.css" scoped></style>