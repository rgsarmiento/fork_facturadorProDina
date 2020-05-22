/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./Resources/assets/js/app.js":
/*!************************************!*\
  !*** ./Resources/assets/js/app.js ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// import SnackbarNotificationQueue from './mixins/SnackbarNotificationQueue';
// import VeeValidate, { Validator } from 'vee-validate';
// import v_es from 'vee-validate/dist/locale/es';
// import es from 'vuetify/es5/locale/es';
// import ElementUI from 'element-ui';
// import Vuetify from 'vuetify';
// import 'vuetify/dist/vuetify.min.css'
// /**
//  * First we will load all of this project's JavaScript dependencies which
//  * includes Vue and other libraries. It is a great starting point when
//  * building robust, powerful web applications using Vue and Laravel.
//  */
// require('./bootstrap');
// window.Vue = require('vue');
// window.EventBus = new Vue();
// // Vuetify es
// Vue.use(Vuetify, {
//     lang: {
//         locales: {es},
//         current: 'es'
//     }
// });
// //  Element UI
// Vue.use(ElementUI);
// // Vee validate
// Vue.use(VeeValidate);
// // Vee es
// Validator.localize('es', v_es);
// // Add errors request
// Vue.prototype.$setLaravelValidationErrorsFromResponse = function(errorResponse) {
//     if (!this.hasOwnProperty('$validator')) return;
//     this.$validator.errors.clear();
//     if (!errorResponse.hasOwnProperty('errors')) return;
//     let errorFields = Object.keys(errorResponse.errors);
//     let form_error = '';
//     if (errorFields.includes('form_error')) form_error += `${errorResponse.errors['form_error'].join()}.`;
//     for (let i = 0; i < errorFields.length; i++) {
//         let field = errorFields[i];
//         let errorString = errorResponse.errors[field].join(', ');
//         this.$validator.errors.add({
//             field: `${form_error}${field}`,
//             msg: errorString
//         });
//     }
// };
// // Add message request
// Vue.prototype.$setLaravelMessage = function(response) {
//     if ((response.hasOwnProperty('success')) && (response.hasOwnProperty('message')) && (!response.success)) this.$root.$emit('addSnackbarNotification', {text: response.message, color: 'error'});
//     if ((response.hasOwnProperty('success')) && (response.hasOwnProperty('message')) && (response.success)) this.$root.$emit('addSnackbarNotification', {text: response.message, color: 'success'});
//     if (response.hasOwnProperty('message') && (!response.hasOwnProperty('success'))) this.$root.$emit('addSnackbarNotification', {text: response.message, color: 'info'});
// };
// // Add errors server
// Vue.prototype.$setLaravelErrors = function(errorResponse) {
//     if ((errorResponse.hasOwnProperty('message')) && (errorResponse.message != '')) this.$root.$emit('addSnackbarNotification', {text: errorResponse.message, color: 'error'});
//     if ((errorResponse.hasOwnProperty('exception')) && (errorResponse.exception != '')) this.$root.$emit('addSnackbarNotification', {text: errorResponse.exception, color: 'error'});
// };
// /**
//  * The following block of code may be used to automatically register your
//  * Vue components. It will recursively scan this directory for the Vue
//  * components and automatically register them with their "basename".
//  *
//  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
//  */
// // const files = require.context('./', true, /\.vue$/i)
// // files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
// Vue.component('tenant-configuration-configuration', require('./components/tenant/configuration/Configuration.vue').default);
// Vue.component('tenant-configuration-documents', require('./components/tenant/configuration/Documents.vue').default);
// Vue.component('tenant-quotation-quotation', require('./components/tenant/quotation/Quotation.vue').default);
// Vue.component('notification-notification', require('./components/notification/Notification.vue').default);
// Vue.component('tenant-document-document', require('./components/tenant/document/Document.vue').default);
// Vue.component('tenant-report-tax', require('./components/tenant/report/tax/TaxReport.vue').default);
// Vue.component('system-company-company', require('./components/system/company/Company.vue').default);
// Vue.component('tenant-quotation-form', require('./components/tenant/quotation/Form.vue').default);
// Vue.component('tenant-client-client', require('./components/tenant/client/Client.vue').default);
// Vue.component('tenant-import-import', require('./components/tenant/import/Import.vue').default);
// // Vue.component('tenant-document-form', require('./components/tenant/document/Form.vue').default);
// Vue.component('tenant-item-item', require('./components/tenant/item/Item.vue').default);
// Vue.component('tenant-tax-tax', require('./components/tenant/tax/Tax.vue').default);
// Vue.component('tenant-logo', require('./components/tenant/logo/Logo.vue').default);
// Vue.component('menu-popover', require('./components/menu/Popover.vue').default);
// Vue.component('auth-login', require('./components/auth/Login.vue').default);
// Vue.component('system-document-company', require('./components/system/document/Document.vue').default);
// /**
//  * Next, we will create a fresh Vue application instance and attach it to
//  * the page. Then, you may begin adding components to this application
//  * or customize the JavaScript scaffolding to fit your unique needs.
//  */
// const app = new Vue({
//     el: '#app',
//     mixins: [SnackbarNotificationQueue],
//     mounted () {
//         EventBus.$on('updateTheme', val => this.theme = val);
//     },
//     data: () => ({
//         drawer: null,
//         theme: false,
//         systemMenus: [],
//         tenantMenus: [{
//             icon: 'fas fa-file-alt',
//             url: '/client/documents',
//             title: 'Documentos'
//         }, {
//             icon: 'fas fa-calculator',
//             url: '/client/quotations',
//             title: 'Cotizaciones'
//         }, {
//             icon: 'people',
//             url: '/client/clients',
//             title: 'Clientes'
//         }, {
//             icon: 'shopping_cart',
//             url: '/client/items',
//             title: 'Productos'
//         }, {
//             icon: 'edit',
//             url: '/client/taxes',
//             title: 'Impuestos'
//         }, {
//             icon: 'settings',
//             url: '/client/configuration',
//             title: 'Configuración'
//         }]
//     })
// });

/***/ }),

/***/ "./Resources/assets/sass/app.scss":
/*!****************************************!*\
  !*** ./Resources/assets/sass/app.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!***************************************************************************!*\
  !*** multi ./Resources/assets/js/app.js ./Resources/assets/sass/app.scss ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! D:\laragon\www\facturadorpro2-co\modules\Factcolombia1\Resources\assets\js\app.js */"./Resources/assets/js/app.js");
module.exports = __webpack_require__(/*! D:\laragon\www\facturadorpro2-co\modules\Factcolombia1\Resources\assets\sass\app.scss */"./Resources/assets/sass/app.scss");


/***/ })

/******/ });