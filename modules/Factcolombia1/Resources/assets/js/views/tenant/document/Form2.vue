<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <!-- <div class="card-header bg-info">
            <h3 class="my-0">Nuevo Comprobante</h3>
        </div> -->
        <div class="tab-content" v-if="loading_form">
            <div class="invoice"> 
                <form autocomplete="off" @submit.prevent="submit">
                    <div class="form-body">
                        <div class="row">
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-6 pb-2">
                                <div class="form-group" :class="{'has-danger': errors.customer_id}">
                                    <label class="control-label font-weight-bold text-info">
                                        Cliente
                                        <a href="#" @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                                    </label>
                                    <el-select v-model="form.customer_id" filterable remote class="border-left rounded-left border-info" popper-class="el-select-customers"
                                        dusk="customer_id"
                                        placeholder="Escriba el nombre o número de documento del cliente"
                                        :remote-method="searchRemoteCustomers"
                                        @keyup.enter.native="keyupCustomer"
                                        :loading="loading_search"
                                        @change="changeCustomer">

                                        <el-option v-for="option in customers" :key="option.id" :value="option.id" :label="option.description"></el-option>

                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.customer_id" v-text="errors.customer_id[0]"></small>
                                </div> 
                            </div> 
                        
                            <div class="col-lg-4 pb-2">
                                <div class="form-group" :class="{'has-danger': errors.type_document_id}">
                                    <label class="control-label">Tipo de factura</label>
                                    <el-select v-model="form.type_document_id" @change="changeDocumentType" popper-class="el-select-document_type" dusk="type_document_id" class="border-left rounded-left border-info">
                                        <el-option v-for="option in type_invoices" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.type_document_id" v-text="errors.type_document_id[0]"></small>
                                </div>
                            </div> 

                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_issue}">
                                    <label class="control-label">Fec. Emisión</label>
                                    <el-date-picker v-model="form.date_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue" :picker-options="datEmision"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_issue" v-text="errors.date_issue[0]"></small>
                                </div>
                            </div>
                            
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_expiration}"> 
                                    <label class="control-label">Fec. Vencimiento</label>
                                    <el-date-picker v-model="form.date_expiration" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_expiration" v-text="errors.date_expiration[0]"></small>
                                </div>
                            </div>
                            
                            
                            <div class="col-lg-2" v-show="form.payment_form_id == 2">
                                <div class="form-group" :class="{'has-danger': errors.time_days_credit}"> 
                                    <label class="control-label">Plazo Credito</label>
                                    <el-input v-model="form.time_days_credit"></el-input>
                                    <small class="form-control-feedback" v-if="errors.time_days_credit" v-text="errors.time_days_credit[0]"></small>
                                </div>
                            </div> 

                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                                    <label class="control-label">Moneda</label>
                                    <el-select v-model="form.currency_type_id" @change="changeCurrencyType">
                                        <el-option v-for="option in currencies" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.currency_type_id" v-text="errors.currency_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.payment_form_id}">
                                    <label class="control-label">Forma de pago</label>
                                    <el-select v-model="form.payment_form_id" >
                                        <el-option v-for="option in payment_forms" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.payment_form_id" v-text="errors.payment_form_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.payment_method_id}">
                                    <label class="control-label">Medio de pago</label>
                                    <el-select v-model="form.payment_method_id" >
                                        <el-option v-for="option in payment_methods" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.payment_method_id" v-text="errors.payment_method_id[0]"></small>
                                </div>
                            </div>  
                        </div>

                        <div class="row mt-2">
 
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Observaciones</label>
                                    <el-input
                                            type="textarea"
                                            autosize
                                            :rows="1"
                                            v-model="form.additional_information">
                                    </el-input>
                                </div>
                            </div> 

                        </div>
  
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="font-weight-bold">Descripción</th>
                                                <th class="text-center font-weight-bold">Unidad</th>
                                                <th class="text-right font-weight-bold">Cantidad</th>
                                                <th class="text-right font-weight-bold">Precio Unitario</th>
                                                <th class="text-right font-weight-bold">Subtotal</th>
                                                <th class="text-right font-weight-bold">Descuento</th>
                                                <th class="text-right font-weight-bold">Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="form.items.length > 0">
                                            <tr v-for="(row, index) in form.items" :key="index">
                                                <td>{{index + 1}}</td>
                                                <td>{{row.item.full_description}} 
                                                    <!-- {{row.item.presentation.hasOwnProperty('description') ? row.item.presentation.description : ''}} -->
                                                    <br/>
                                                    <small>{{row.tax.name}}</small>
                                                </td>
                                                <td class="text-center">{{row.item.unit_type_id}}</td>

                                                <td class="text-right">{{row.quantity}}</td>
                                                <!--<td class="text-right" v-else ><el-input-number :min="0.01" v-model="row.quantity"></el-input-number> </td> -->

                                                <td class="text-right">{{ratePrefix()}} {{getFormatUnitPriceRow(row.price)}}</td>
                                                <!--<td class="text-right" v-else ><el-input-number :min="0.01" v-model="row.unit_price"></el-input-number> </td> -->


                                                <td class="text-right">{{ratePrefix()}} {{row.subtotal}}</td>
                                                <td class="text-right">{{ratePrefix()}} {{ row.discount }}</td>
                                                <td class="text-right">{{ratePrefix()}} {{row.total}}</td>
                                                <td class="text-right">
                                                    <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
                                                    <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click="ediItem(row, index)" ><span style='font-size:10px;'>&#9998;</span> </button>

                                                </td>
                                            </tr>
                                            <tr><td colspan="9"></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-6 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="clickAddItemInvoice">+ Agregar Producto</button>
                                    <button type="button" class="ml-3 btn waves-effect waves-light btn-primary" @click.prevent="clickAddItemInvoice">+ Agregar Retención</button>
                                </div>
                            </div>
 
                            <div class="col-md-12" style="display: flex; flex-direction: column; align-items: flex-end;">
                                <table>
 
                                    <tr v-if="form.sale > 0">
                                        <td>TOTAL VENTA</td>
                                        <td>:</td>
                                        <td class="text-right">{{ratePrefix()}} {{ form.sale }}</td>
                                    </tr> 
                                    <tr v-if="form.total_discount > 0">
                                        <td>TOTAL DESCUENTO (-)</td>
                                        <td>:</td>
                                        <td class="text-right">{{ratePrefix()}} {{ form.total_discount }}</td>
                                    </tr>
                                    <template v-for="(tax, index) in form.taxes">
                                        <tr v-if="((tax.total > 0) && (!tax.is_retention))" :key="index">
                                            <td >
                                                {{tax.name}}(+)
                                            </td>
                                            <td>:</td>
                                            <td class="text-right">{{ratePrefix()}} {{Number(tax.total).toFixed(2)}}</td>
                                        </tr>
                                    </template>
                                    <tr v-if="form.subtotal > 0">
                                        <td>SUBTOTAL</td>
                                        <td>:</td>
                                        <td class="text-right">{{ratePrefix()}} {{ form.subtotal }}</td>
                                    </tr> 

                                </table>

                                <template v-if="form.total > 0">
                                    <h3 class="text-right" v-if="form.total > 0"><b>TOTAL: </b>{{ratePrefix()}} {{ form.total }}</h3>
                                </template>
                            </div>

                        </div>

                    </div>


                    <div class="form-actions text-right mt-4">
                        <el-button @click.prevent="close()">Cancelar</el-button>
                        <el-button class="submit" type="primary" native-type="submit" :loading="loading_submit" v-if="form.items.length > 0">Generar</el-button>
                    </div>
                </form>
            </div>

        <document-form-item :showDialog.sync="showDialogAddItem"
                           :recordItem="recordItem"
                           :isEditItemNote="false"
                           :operation-type-id="form.operation_type_id"
                           :currency-type-id-active="form.currency_type_id"
                           :exchange-rate-sale="form.exchange_rate_sale"
                           :typeUser="typeUser"
                           :configuration="configuration"
                           @add="addRow"></document-form-item>

        <person-form :showDialog.sync="showDialogNewPerson"
                       type="customers"
                       :external="true"
                       :input_person="input_person"
                       :type_document_id = form.type_document_id></person-form>

        <!-- <document-options :showDialog.sync="showDialogOptions"
                          :recordId="documentNewId"
                          :isContingency="is_contingency"
                          :showClose="false"></document-options> -->

 


    </div>
    </div>
</template>

<style>
.input-custom{
    width: 50% !important;
}

.el-textarea__inner {
    height: 65px !important;
    min-height: 65px !important;
}
</style>
<script>
    import DocumentFormItem from './partials/item.vue'
    import PersonForm from '@views/persons/form.vue'
    // import DocumentOptions from '../documents/partials/options.vue'
    import {functions, exchangeRate} from '@mixins/functions'
    // import {calculateRowItem} from '../../../helpers/functions' 

    export default {
        props: ['typeUser', 'configuration'],
        components: {PersonForm, DocumentFormItem},
        mixins: [functions, exchangeRate],
        data() {
            return {
                datEmision: {
                  disabledDate(time) {
                    return time.getTime() > moment();
                  }
                },
                input_person:{},
                company:{},
                is_client:false,
                recordItem: null,
                resource: 'documents-co',
                showDialogAddItem: false,
                showDialogNewPerson: false,
                showDialogOptions: false,
                loading_submit: false,
                loading_form: false,
                errors: {},
                form: {},
                type_invoices: [],
                currencies: [],
                all_customers: [],
                payment_methods: [],
                payment_forms: [],
                form_payment: {},
                customers: [],
                all_series: [],
                series: [],
                currency_type: {},
                documentNewId: null,
                total_global_discount:0,
                loading_search:false,
                taxes:  [],

            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.all_customers = response.data.customers;
                    this.taxes = JSON.parse(response.data.taxes);
                    // console.log(this.taxes)
                    this.type_invoices = response.data.type_invoices;
                    this.currencies = response.data.currencies
                    this.payment_methods = response.data.payment_methods
                    this.payment_forms = response.data.payment_forms
                    this.form.currency_type_id = (this.currencies.length > 0)?this.currencies[0].id:null;
                    this.form.type_document_id = (this.type_invoices.length > 0)?this.type_invoices[0].id:null;
                    this.form.payment_form_id = (this.payment_forms.length > 0)?this.payment_forms[0].id:null;
                    this.form.payment_method_id = (this.payment_methods.length > 0)?this.payment_methods[0].id:null;

                    // this.selectDocumentType()
                    this.filterCustomers();

                    // this.changeEstablishment()
                    // this.changeDateOfIssue()
                    // this.changeDocumentType()
                    // this.changeCurrencyType()
                })
            this.loading_form = true
            this.$eventHub.$on('reloadDataPersons', (customer_id) => {
                this.reloadDataCustomers(customer_id)
            })
            this.$eventHub.$on('initInputPerson', () => {
                this.initInputPerson()
            })
        },
        watch: {
            // form: {
            //     handler(val) {

            //         // console.log(val)
            //         val.taxes = JSON.parse(JSON.stringify(this.taxes));

            //         val.items.forEach(item => {
            //             item.tax = this.taxes.find(tax => tax.id == item.tax_id);

            //             if (
            //                 item.discount == null ||
            //                 item.discount == "" ||
            //                 item.discount > item.price * item.quantity
            //             )
            //                 this.$set(item, "discount", 0);

            //             item.total_tax = 0;

            //             if (item.tax != null) {
            //                 let tax = val.taxes.find(tax => tax.id == item.tax.id);

            //                 if (item.tax.is_fixed_value)

            //                     item.total_tax = (
            //                         item.tax.rate * item.quantity -
            //                         (item.discount < item.price * item.quantity ? item.discount : 0)
            //                     ).toFixed(2);

            //                 if (item.tax.is_percentage)

            //                     item.total_tax = (
            //                         (item.price * item.quantity -
            //                         (item.discount < item.price * item.quantity
            //                             ? item.discount
            //                             : 0)) *
            //                         (item.tax.rate / item.tax.conversion)
            //                     ).toFixed(2);

            //                 if (!tax.hasOwnProperty("total")) 
            //                     tax.total = Number(0).toFixed(2);

            //                 tax.total = (Number(tax.total) + Number(item.total_tax)).toFixed(2);
            //             }

            //             item.subtotal = (
            //                 Number(item.price * item.quantity) + Number(item.total_tax)
            //             ).toFixed(2);

            //             this.$set(
            //                 item,
            //                 "total",
            //                 (Number(item.subtotal) - Number(item.discount)).toFixed(2)
            //             );
                        
            //         });

            //         val.subtotal = val.items
            //             .reduce(
            //                 (p, c) => Number(p) + (Number(c.subtotal) - Number(c.discount)),
            //                 0
            //             )
            //             .toFixed(2);
            //             val.sale = val.items
            //             .reduce(
            //                 (p, c) =>
            //                 Number(p) + Number(c.price * c.quantity) - Number(c.discount),
            //                 0
            //             )
            //             .toFixed(2);
            //             val.total_discount = val.items
            //             .reduce((p, c) => Number(p) + Number(c.discount), 0)
            //             .toFixed(2);
            //             val.total_tax = val.items
            //             .reduce((p, c) => Number(p) + Number(c.total_tax), 0)
            //             .toFixed(2);

            //         let total = val.items
            //             .reduce((p, c) => Number(p) + Number(c.total), 0)
            //             .toFixed(2);

            //         let totalRetentionBase = Number(0);

            //         this.taxes.forEach(tax => {
            //             if (tax.is_retention && tax.in_base && tax.apply) {
            //                 tax.retention = (
            //                 Number(val.sale) *
            //                 (tax.rate / tax.conversion)
            //                 ).toFixed(2);

            //                 totalRetentionBase =
            //                 Number(totalRetentionBase) + Number(tax.retention);

            //                 if (Number(totalRetentionBase) >= Number(val.sale))
            //                 this.$set(tax, "retention", Number(0).toFixed(2));

            //                 total -= Number(tax.retention).toFixed(2);
            //             }

            //             if (
            //                 tax.is_retention &&
            //                 !tax.in_base &&
            //                 tax.in_tax != null &&
            //                 tax.apply
            //             ) {
            //                 let row = val.taxes.find(row => row.id == tax.in_tax);

            //                 tax.retention = Number(
            //                 Number(row.total) * (tax.rate / tax.conversion)
            //                 ).toFixed(2);

            //                 if (Number(tax.retention) > Number(row.total))
            //                 this.$set(tax, "retention", Number(0).toFixed(2));

            //                 row.retention = Number(tax.retention).toFixed(2);
            //                 total -= Number(tax.retention).toFixed(2);
            //             }
            //         });

            //         val.total = Number(total).toFixed(2);
            //     },
            //     deep: true
            // },
            typeDocuments: {
                // handler(val) {
                //     val.forEach(row => {
                //     if (row.alert_range)
                //         this.$root.$emit("addSnackbarNotification", {
                //         text: `La resolución de ${row.name} esta próxima a vencer por rango.`,
                //         color: "warning"
                //         });
                //     if (row.alert_date)
                //         this.$root.$emit("addSnackbarNotification", {
                //         text: `La resolución de ${row.name} esta próxima a vencer por fecha de vigencia.`,
                //         color: "warning"
                //         });
                //     });
                // },
                // deep: true
            }
        },
        methods: {   
            ratePrefix(tax = null) {
                if ((tax != null) && (!tax.is_fixed_value)) return null;
                
                return (this.company.currency != null) ? this.company.currency.symbol : '$';
            },
            keyupCustomer(){

                if(this.input_person.number){

                    if(!isNaN(parseInt(this.input_person.number))){

                        switch (this.input_person.number.length) {
                            case 8:
                                this.input_person.identity_type_document_id = '1'
                                this.showDialogNewPerson = true
                                break;

                            case 11:
                                this.input_person.identity_type_document_id = '6'
                                this.showDialogNewPerson = true
                                break;
                            default:
                                this.input_person.identity_type_document_id = '6'
                                this.showDialogNewPerson = true
                                break;
                        }
                    }
                }
            }, 
            clickAddItemInvoice(){
                this.recordItem = null
                this.showDialogAddItem = true
            },
            getFormatUnitPriceRow(unit_price){
                return _.round(unit_price, 6)
                // return unit_price.toFixed(6)
            },  
            ediItem(row, index)
            {
                row.indexi = index
                this.recordItem = row
                this.showDialogAddItem = true

            },
            searchRemoteCustomers(input) {

                if (input.length > 0) {
                // if (input!="") {
                    // console.log("a")
                    this.loading_search = true
                    let parameters = `input=${input}&type_document_id=${this.form.type_document_id}&operation_type_id=${this.form.operation_type_id}`

                    this.$http.get(`/${this.resource}/search/customers?${parameters}`)
                            .then(response => {
                                this.customers = response.data.customers
                                this.loading_search = false
                                this.input_person.number = null

                                if(this.customers.length == 0){
                                    // console.log("b")
                                    this.filterCustomers()
                                    this.input_person.number = input
                                }
                            })
                } else {
                    // this.customers = []
                    this.filterCustomers()
                    this.input_person.number = null
                }

            },
            initForm() {
                
                this.form = {
                    type_document_id: null,
                    currency_id: null,
                    date_issue: moment().format('YYYY-MM-DD'),
                    date_expiration: null,
                    type_invoice_id: 1,
                    total_discount: 0,
                    total_tax: 0,
                    watch: false,
                    subtotal: 0,
                    items: [],
                    total: 0,
                    sale: 0,
                    time_days_credit: 0
                }

                this.errors = {}  
                this.$eventHub.$emit('eventInitForm')

                this.initInputPerson()

            },
            initInputPerson(){
                this.input_person = {
                    number:null,
                    identity_type_document_id:null
                }
            },
            resetForm() {
                this.activePanel = 0
                this.initForm()
                this.form.currency_id = (this.currencies.length > 0)?this.currencies[0].id:null
                // this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                this.form.type_document_id = (this.type_invoices.length > 0)?this.type_invoices[0].id:null
                // this.form.operation_type_id = (this.operation_types.length > 0)?this.operation_types[0].id:null
                // this.selectDocumentType()
                // this.changeEstablishment()
                // this.changeDocumentType()
                // this.changeDateOfIssue()
                // this.changeCurrencyType()
            },
            async changeOperationType() {
                await this.filterCustomers();
                await this.setDataDetraction();
            }, 
            changeEstablishment() {
                this.establishment = _.find(this.establishments, {'id': this.form.establishment_id})
                this.filterSeries()
            },
            changeDocumentType() {
                this.filterSeries();
                this.cleanCustomer();
                this.filterCustomers();
            },
            cleanCustomer(){
                this.form.customer_id = null
                // this.customers = []
            },
            changeDateOfIssue() {
            //   if(moment(this.form.date_of_issue) < moment().day(-1) && this.configuration.restrict_receipt_date) {
            //     this.$message.error('No puede seleccionar una fecha menor a 6 días.');
            //     this.dateValid=false
            //   } else { this.dateValid = true }
                this.form.date_expiration = this.form.date_of_issue
                this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                    this.form.exchange_rate_sale = response
                })
            },
            assignmentDateOfPayment(){
                this.form.payments.forEach((payment)=>{
                    payment.date_of_payment = this.form.date_of_issue
                })
            },

            filterSeries() {
                this.form.series_id = null
                this.series = _.filter(this.all_series, {'establishment_id': this.form.establishment_id,
                                                         'type_document_id': this.form.type_document_id,
                                                         'contingency': this.is_contingency});
                this.form.series_id = (this.series.length > 0)?this.series[0].id:null
            },
            filterCustomers() {
                // this.form.customer_id = null
                // if(this.form.operation_type_id === '0101' || this.form.operation_type_id === '1001') {
                //     if(this.form.type_document_id === '01') {
                //         this.customers = _.filter(this.all_customers, {'identity_type_document_id': '6'})
                //     } else {
                //         if(this.document_type_03_filter) {
                //             this.customers = _.filter(this.all_customers, (c) => { return c.identity_type_document_id !== '6' })
                //         } else {
                //             this.customers = this.all_customers
                //         }
                //     }
                // } else {
                    this.customers = this.all_customers
                // }
            }, 
            addRow(row) {
                if(this.recordItem)
                {
                    //this.form.items.$set(this.recordItem.indexi, row)
                    this.form.items[this.recordItem.indexi] = row
                    this.recordItem = null
                }
                else{
                    this.form.items.push(JSON.parse(JSON.stringify(row)));
                }
                console.log(this.form)
                this.calculateTotal();
            },
            clickRemoveItem(index) {
                this.form.items.splice(index, 1)
                this.calculateTotal()
            },
            changeCurrencyType() {
                this.currency_type = _.find(this.currencies, {'id': this.form.currency_type_id})
                let items = []
                this.form.items.forEach((row) => {
                    items.push(calculateRowItem(row, this.form.currency_type_id, this.form.exchange_rate_sale))
                });
                this.form.items = items
                this.calculateTotal()
            },
            calculateTotal() {
                
                this.setDataTotals() 

            }, 
            setDataTotals() {

                // console.log(val)
                let val = this.form
                val.taxes = JSON.parse(JSON.stringify(this.taxes));

                val.items.forEach(item => {
                    item.tax = this.taxes.find(tax => tax.id == item.tax_id);

                    if (
                        item.discount == null ||
                        item.discount == "" ||
                        item.discount > item.price * item.quantity
                    )
                        this.$set(item, "discount", 0);

                    item.total_tax = 0;

                    if (item.tax != null) {
                        let tax = val.taxes.find(tax => tax.id == item.tax.id);

                        if (item.tax.is_fixed_value)

                            item.total_tax = (
                                item.tax.rate * item.quantity -
                                (item.discount < item.price * item.quantity ? item.discount : 0)
                            ).toFixed(2);

                        if (item.tax.is_percentage)

                            item.total_tax = (
                                (item.price * item.quantity -
                                (item.discount < item.price * item.quantity
                                    ? item.discount
                                    : 0)) *
                                (item.tax.rate / item.tax.conversion)
                            ).toFixed(2);

                        if (!tax.hasOwnProperty("total")) 
                            tax.total = Number(0).toFixed(2);

                        tax.total = (Number(tax.total) + Number(item.total_tax)).toFixed(2);
                    }

                    item.subtotal = (
                        Number(item.price * item.quantity) + Number(item.total_tax)
                    ).toFixed(2);

                    this.$set(
                        item,
                        "total",
                        (Number(item.subtotal) - Number(item.discount)).toFixed(2)
                    );
                    
                });

                val.subtotal = val.items
                    .reduce(
                        (p, c) => Number(p) + (Number(c.subtotal) - Number(c.discount)),
                        0
                    )
                    .toFixed(2);
                    val.sale = val.items
                    .reduce(
                        (p, c) =>
                        Number(p) + Number(c.price * c.quantity) - Number(c.discount),
                        0
                    )
                    .toFixed(2);
                    val.total_discount = val.items
                    .reduce((p, c) => Number(p) + Number(c.discount), 0)
                    .toFixed(2);
                    val.total_tax = val.items
                    .reduce((p, c) => Number(p) + Number(c.total_tax), 0)
                    .toFixed(2);

                let total = val.items
                    .reduce((p, c) => Number(p) + Number(c.total), 0)
                    .toFixed(2);

                let totalRetentionBase = Number(0);

                this.taxes.forEach(tax => {
                    if (tax.is_retention && tax.in_base && tax.apply) {
                        tax.retention = (
                        Number(val.sale) *
                        (tax.rate / tax.conversion)
                        ).toFixed(2);

                        totalRetentionBase =
                        Number(totalRetentionBase) + Number(tax.retention);

                        if (Number(totalRetentionBase) >= Number(val.sale))
                        this.$set(tax, "retention", Number(0).toFixed(2));

                        total -= Number(tax.retention).toFixed(2);
                    }

                    if (
                        tax.is_retention &&
                        !tax.in_base &&
                        tax.in_tax != null &&
                        tax.apply
                    ) {
                        let row = val.taxes.find(row => row.id == tax.in_tax);

                        tax.retention = Number(
                        Number(row.total) * (tax.rate / tax.conversion)
                        ).toFixed(2);

                        if (Number(tax.retention) > Number(row.total))
                        this.$set(tax, "retention", Number(0).toFixed(2));

                        row.retention = Number(tax.retention).toFixed(2);
                        total -= Number(tax.retention).toFixed(2);
                    }
                });

                val.total = Number(total).toFixed(2)

            },
            close() {
                location.href = (this.is_contingency) ? `/contingencies` : `/${this.resource}`
            },
            reloadDataCustomers(customer_id) {
                // this.$http.get(`/${this.resource}/table/customers`).then((response) => {
                //     this.customers = response.data
                //     this.form.customer_id = customer_id
                // })
                this.$http.get(`/${this.resource}/search/customer/${customer_id}`).then((response) => {
                    this.customers = response.data.customers
                    this.form.customer_id = customer_id
                })
            },
             changeCustomer() {
                // this.customer_addresses = [];
                // let customer = _.find(this.customers, {'id': this.form.customer_id});
                // this.customer_addresses = customer.addresses;
                // if(customer.address)
                // {
                //     this.customer_addresses.unshift({
                //         id:null,
                //         address: customer.address
                //     })
                // }


                /*if(this.customer_addresses.length > 0) {
                    let address = _.find(this.customer_addresses, {'main' : 1});
                    this.form.customer_address_id = address.id;
                }*/
            }
        }
    }
</script>
