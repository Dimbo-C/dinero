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
    export default {
        data() {
            return {
                form: new Form({
                    firstName: "",
                    lastName: "",
                    middleName: "",
                    birthDate: "",
                    passport: "",
                    login: this.$route.params.wallet,
                }),
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
                            this.form.firstName = data.firstName;
                            this.form.lastName = data.lastName;
                            this.form.middleName = data.middleName;
                            this.form.birthDate = data.birthDate;
                            this.form.passport = data.passport;
                            this.isLoaded = true;
                        })
            },

            updateIdentification(){
                Dinero.post(`/api/qiwi-wallets/${this.$route.params.wallet}/identification`, this.form)
                        .then((data) => {
                            console.log(data);

                            const string = data.code;
                            const result = string.match(/error/i);
                            if (result) {
                                Bus.$emit('showNotification', "danger", "Не удалось обновить идентификационные данные, ошибка сервера");
                            }

                        });
            },

            showSetting(tabName){
                this.$parent.tab = tabName;
            },
        }
    }
</script>