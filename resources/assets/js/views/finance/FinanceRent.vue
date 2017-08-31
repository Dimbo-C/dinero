<template>
    <div>
        <page-header icon="fa-money" title="Счета аренды">
            <router-link tag="li" to="/finance">
                <a>Финансы</a>
            </router-link>
        </page-header>

        <div class="container-fluid">
            <div class="m-b-lg">
                <div class="btn-group p-b-xs">
                    <button class="btn btn-success">
                        Выставить счет
                    </button>
                </div>
            </div>

            <div class="table-responsive">
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
                        <th>ID</th>
                        <th>Логин</th>
                        <th>Длительность</th>
                        <th>Цена за месяц (руб.)</th>
                        <th>Сумма к оплате (руб.)</th>
                        <th>Статус</th>
                        <th>Кем выставлено</th>
                        <th>Действителен</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="invoice in invoices">
                        <td>
                            <div class="checkbox m-none">
                                <label class="p-t-xs">
                                    <input type="checkbox" v-model="selected" :value="invoice.id">
                                </label>
                            </div>
                        </td>
                        <td v-text="invoice.id"></td>
                        <td v-text="invoice.login"></td>
                        <td v-text="invoice.duration"></td>
                        <td>{{ invoice.month_price | currency }}</td>
                        <td>{{ invoice.amount | currency }}</td>
                        <td v-text="invoice.status"></td>
                        <td v-text="invoice.invoice_person"></td>
                        <td v-text="invoice.until"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
  import Table from './../../mixins/table';

  export default {
    mixins: [Table],
    data() {
      return {
        invoices: [
          {
            id: 1,
            login: '',
            duration: '12 мес.',
            month_price: '1000',
            amount: '12000',
            status: 'Время истекло',
            invoice_person: '',
            until: '27.07.2017 10:30',
          },
        ],
      };
    },
    mounted () {
      this.initTable(this.invoices);
    },
  }
</script>