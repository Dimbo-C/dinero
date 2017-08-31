import $ from 'jquery';

/**
 * Initialize the Imprinx form extension points.
 */
Dinero.forms = {
    register: {},
    updateContactInformation: {},
    updateTeamMember: {}
};

/**
 * Load the Form helper class.
 */
require('./form');

/**
 * Define the FormError collection class.
 */
require('./errors');

/**
 * Add additional HTTP / form helpers to the Imprinx object.
 */
$.extend(Dinero, require('./http'));
