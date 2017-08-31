import Vue from 'vue';

/**
 * Format the given money value.
 *
 * Source: https://github.com/vuejs/vue/blob/1.0/src/filters/index.js#L70
 */
Vue.filter('currency', (value, symbol = false) => {
  value = parseFloat(value);

  if (!symbol) {
    symbol = window.Dinero.currencySymbol
  }

  if (! isFinite(value) || (! value && value !== 0)){
    return '';
  }

  var stringified = Math.abs(value).toFixed(2);

  var _int = stringified.slice(0, -1 - 2);

  var i = _int.length % 3;

  var head = i > 0
    ? (_int.slice(0, i) + (_int.length > 3 ? ' ' : ''))
    : '';

  var _float = stringified.slice(-1 - 2);

  var sign = value < 0 ? '-' : '';

  return sign + head +
    _int.slice(i).replace(/(\d{3})(?=\d)/g, '$1,') +
    _float + ' ' + symbol;
});