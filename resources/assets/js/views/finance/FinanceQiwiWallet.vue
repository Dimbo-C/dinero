<template>
    <div>
        <page-header icon="fa-money" title="Qiwi Visa Wallet">
            <router-link tag="li" to="/finance">
                <a>Финансы</a>
            </router-link>
        </page-header>

        <div class="container-fluid">
            <div class="m-b-lg">
                <div class="btn-group p-b-xs">
                    <router-link to="/finance/qiwi/dashboard" class="btn btn-success">
                        Панель управления
                    </router-link>
                </div>

                <div class="btn-group p-b-xs">
                    <button class="btn btn-success" @click="showModal('modal-personal-browsing')">Персональный просмотр</button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Финасовая информация:
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item">Оборот за месяц (руб.):</li>
                            <li class="list-group-item">Оборот за сегодня:</li>
                            <li class="list-group-item">Оборот за вчера:</li>
                            <li class="list-group-item">Доступные к выводу средства (на данный момент):</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <personal-browsing-modal></personal-browsing-modal>
    </div>
</template>

<script>
  import PersonalBrowsingModal from './PersonalBrowsingModal.vue';

  export default {
    components: {
      PersonalBrowsingModal,
    },
    methods: {
      showModal (id) {
        $(`#${id}`).modal('show');
      }
    }
  };
</script>