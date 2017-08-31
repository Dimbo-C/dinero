module.exports = {
  data() {
    return {
      items: [],
      selected: [],
    };
  },
  methods: {
    initTable(items) {
      if (items.length) {
        this.items = items;
      }
    },
    unselectAll() {
      this.selected = [];
    }
  },
  computed: {
    selectAll: {
      get() {
        return this.items ? this.selected.length === this.items.length : false;
      },
      set(value) {
        let selected = [];

        if (value) {
          this.items.forEach((item) => {
            selected.push(item);
          });
        }

        this.selected = selected;
      }
    }
  }
};