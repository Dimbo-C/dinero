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

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-12 col-md-8">
                                    Идентификация кошелька Qiwi ({{ form.login}})
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <button class="btn btn-primary full-width marginless paddingless"
                                            @click="showSetting('main')">
                                        Настройки
                                    </button>
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <button class="btn btn-primary full-width marginless paddingless"
                                            @click="showSetting('security')">
                                        Безопасность
                                    </button>
                                </div>
                            </div>
                        </div>

                        <loading :show="!isLoaded"></loading>
                        <div v-if="isLoaded" class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Имя</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: Иван"
                                               v-model="form.firstName">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Фамилия</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: Иванов"
                                               v-model="form.lastName">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Отчество</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: Иванович"
                                               v-model="form.middleName">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Дата рождения</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: 31.12.1999"
                                               v-model="form.birthDate">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Серия и номер паспорта</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: 4400 111222"
                                               v-model="form.passport">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">ИНН</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: 7710000001"
                                               v-model="form.inn">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">ОМС</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: 00000000178"
                                               v-model="form.oms">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">СНИЛС</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: 12345678901"
                                               v-model="form.snils">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Тип идентификации</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Например: SIMPLE"
                                               disabled
                                               v-model="type">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <button class="btn btn-primary"
                                                @click="updateIdentification">
                                            Обновить
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import * as _ from "lodash";
    export default {
        data() {
            return {
                form: new Form({
                    firstName: "",
                    lastName: "",
                    middleName: "",
                    birthDate: "",
                    passport: "",
                    inn: "",
                    oms: "",
                    snils: "",
                    login: this.$route.params.wallet,
                }),
                type: "",
                isLoaded: false,
            }
        },
        mounted() {
            this.fetchOwnerData();
        },
        methods: {
            fetchOwnerData(){
                axios.get(`/api/qiwi-wallets/${this.$route.params.wallet}/identification`)
                        .then((response) => {
                            console.log(response);
                            const data = response.data;
//                            this.form = data;
//                            console.log("before assign");
//                            console.log(this.form);
//                            console.log(data);
//
////                            _.merge(this.form, data);
//                            console.log("after assign");
//                            console.log(this.form);
//                            console.log(data);
                            this.form.firstName = data.firstName;
                            this.form.lastName = data.lastName;
                            this.form.middleName = data.middleName;
                            this.form.birthDate = data.birthDate;
                            this.form.passport = data.passport;
                            this.form.inn = data.inn;
                            this.form.oms = data.oms;
                            this.form.snils = data.snils;
                            this.type = data.type;

                            this.isLoaded = true;
                        })
            },

            updateIdentification(){
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/identification`, this.form)
                        .then((data) => {
                            console.log(data);

                            if ("code" in data) {
//                                const string = data.code;
//                                const result = string.match(/error/i);
//                                if (result) {
                                Bus.$emit('showNotification', "danger", "Не удалось обновить идентификационные данные, ошибка сервера");
//                                }
                            } else {
                                Bus.$emit('showNotification', "success", "Персональные данные обновлены");
                            }
                        });
            },

            showSetting(tabName){
                this.$parent.tab = tabName;
            },
        }
    }
</script>