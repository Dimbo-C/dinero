import Vue from 'vue';

export default Vue.extend({
  methods: {
    init(modalId) {
      const self = this;

      this.$nextTick(() => {
        $(`#${modalId}`)
          .on('hidden.bs.modal', () => {
            self.reset(); // в компоненте
            self.changeTabTo(`${modalId}-credentials`);
          })
          .on('show.bs.modal', () => {
            self.generateCredentials();
          });
      })
    },

    closeModal(modalId) {
      $(`#${modalId}`).modal('hide');
    },

    changeTabTo(tabId) {
      $(`a[href="#${tabId}"]`).tab('show');
    },

    generateCredentials() {
      let loginDigits = this.randomInteger(1, 9999);
      this.form.login = this.loginPrefix + this.setPad(loginDigits.toString());
      this.form.password = this.randomString(10);
    },

    randomString(length) {
      let text = " ";
      const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789";

      for( let i = 0; i < length; i += 1 ) {
        text += charset.charAt(Math.floor(Math.random() * charset.length));
      }

      return text;
    },

    randomInteger(min, max) {
      let rand = min - 0.5 + Math.random() * (max - min + 1);
      rand = Math.round(rand);

      return rand;
    },

    setPad(str) {
      const pad = "0000";
      return pad.substring(0, pad.length - str.length) + str;
    }
  },
});