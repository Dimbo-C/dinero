<template>
    <!-- Add Admin Modal -->
    <div class="modal fade" id="modal-send-message-admin" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Добавить администратора</h4>
                </div>
                <div class="modal-body p-b-none">
                    <div class="form">
                        <!-- Message -->
                        <div class="form-group" v-if="!showConfirmation">
                            <label class="control-label">Сообщение</label>
                            <textarea class="form-control"
                                      rows="5"
                                      v-model="form.message"
                                      autofocus=""
                                      required
                            ></textarea>
                        </div>
                        <div class="form-group" v-else>
                            <label>Вы уверены, что хотите отправить это сообщение?</label>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="modal-footer border-none">
                    <button class="btn btn-default pull-left" @click="cancel()">Отменить</button>
                    <button type="button" class="btn btn-primary pull-right" @click="submitForm()">
                        <span v-if="!showConfirmation">Отправить сообщение</span>
                        <span v-else>Да, отправить сообщение</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Modal from './../modal';

  export default Modal.extend({
    data() {
      return {
        showConfirmation: false,
        form: new Form({
          message: '',
        }),
      };
    },
    methods: {
      reset() {
        this.form = new Form({
          message: '',
        });
      },
      submitForm() {
        if (!this.showConfirmation) {
          this.showConfirmation = true;
        } else {
          $('#modal-send-message-admin').modal('hide');
          this.$emit('unselect');
        }
      },
      cancel() {
        if (this.showConfirmation) {
          this.showConfirmation = false;
        } else {
          $('#modal-send-message-admin').modal('hide');
        }
      }
    },
  });
</script>