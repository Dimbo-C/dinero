<template>
    <!-- Add Proxy For System Modal -->
    <div class="modal fade" id="modal-add-proxy-system" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Добавить прокси</h4>
                </div>
                <div class="modal-body p-b-none">

                    <div class="form-horizontal">

                        <!-- Proxies List -->
                        <div class="form-group">
                            <label for="proxy-list" class="col-md-4 control-label">Список прокси</label>

                            <div class="col-md-8">
                                <textarea id="proxy-list"
                                          class="form-control"
                                          rows="6"
                                          v-model="proxies_string"
                                          :placeholder="proxiesPlaceholder"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="modal-footer border-none">
                    <button class="btn btn-default pull-left" data-dismiss="modal">Отменить</button>
                    <button type="button"
                            class="btn btn-primary pull-right"
                            @click="submitForm()"
                    >Добавить</button>
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
          proxies: this.proxies,
        }),

        proxies_string: '',

        proxiesPlaceholder: 'host1:port1 login1:password1' + '\r\n' + 'ip2:port2 login2:password2',
      };
    },
    watch: {
      proxies (val) {
        this.form.proxies = val;
      }
    },

    methods: {
        submitForm() {
          Dinero.post('/api/proxies', this.form)
            .then((response) => {
                this.$emit('add-proxies', response);

                this.proxies_string = '';
                this.price = '';
                this.using_type = 'own';
            })
        },
    },

    computed: {
      proxies() {
        let proxies = this.proxies_string.split('\n');
        proxies = proxies.filter(p => p !== '');

        return proxies.map((p) => {
          p = p.split(' ');
          const hp = p[0].split(':');
          const lp = p[1] ? p[1].split(':') : null;

          return {
            host: hp[0],
            port: hp[1] ? hp[1] : '',
            login: lp ? lp[0] : '',
            password: lp ? lp[1] ? lp[1] : '' : '',
            type: 'system',
            using_type:'own',
            price: this.price
          };
        });
      }
    }
  };
</script>