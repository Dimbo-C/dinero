<template>
    <modal id="modal-personal-browsing" title="Список сотрудников">
        <template slot="modal-body">
            <table class="table">
                <tbody>
                <tr>
                    <td>
                        <strong>co0001</strong>
                        <a href="/" class="btn btn-default btn-sm pull-right" target="_blank">
                            Перейти в панель управления
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>co0002</strong>
                        <a href="/" class="btn btn-default btn-sm pull-right" target="_blank">
                            Перейти в панель управления
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>co0003</strong>
                        <a href="/" class="btn btn-default btn-sm pull-right" target="_blank">
                            Перейти в панель управления
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>co0004</strong>
                        <a href="/" class="btn btn-default btn-sm pull-right" target="_blank">
                            Перейти в панель управления
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>

        <div class="modal-footer" slot="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
    </modal>
</template>

<script>
  export default {
      /*
       * The component's data.
       */
    data() {
      return {
        //
      };
    },

    /**
     * Prepare the component.
     */
    mounted() {
      this.prepareComponent();
    },

    methods: {
      /**
       * Prepare the component.
       */
      prepareComponent() {
        //
      },
    }
  }
</script>