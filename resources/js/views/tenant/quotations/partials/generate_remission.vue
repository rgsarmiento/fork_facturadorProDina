<template>
    <div>
        <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
    
            <div class="row" v-if="generate"> 

                <div class="col-lg-6">
                    <div class="form-group" :class="{'has-danger': errors.date_issue}">
                        <label class="control-label">Fecha de emisión</label>
                        <el-date-picker readonly v-model="document.date_issue" type="date" value-format="yyyy-MM-dd" @change="changeDateOfIssue"></el-date-picker>
                        <small class="form-control-feedback" v-if="errors.date_issue" v-text="errors.date_issue[0]"></small>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group" :class="{'has-danger': errors.date_expiration}">
                        <label class="control-label">Fecha de vencimiento</label>
                        <el-date-picker v-model="document.date_expiration" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                        <small class="form-control-feedback" v-if="errors.date_expiration" v-text="errors.date_expiration[0]"></small>
                    </div>
                </div>
                <br>

            </div>

            <span slot="footer" class="dialog-footer">
                <el-button @click="clickClose">Cerrar</el-button>
                <el-button class="submit" type="primary" @click="submit" :loading="loading_submit">Generar</el-button>
            </span>
        </el-dialog>

        <document-options :showDialog.sync="showDialogDocumentOptions" :recordId="documentNewId" :showDownload="true" :isContingency="false" :showClose="true"></document-options>

    </div>
</template>

<script>
import DocumentOptions from '@viewsModuleProColombia/tenant/document/partials/options.vue'

export default {
    components: {
        DocumentOptions,
    },

    props: ["showDialog", "recordId"],
    data() {
        return {
            customer_email: "",
            titleDialog: null,
            loading: false,
            resource: "quotations",
            resource_documents: "co-documents",
            errors: {},
            form: {},
            document: {},
            document_types: [],
            all_document_types: [],
            all_series: [],
            series: [],
            customers: [],
            generate: false,
            loading_submit: false,
            showDialogDocumentOptions: false,
            showDialogSaleNoteOptions: false,
            documentNewId: null,
            is_document_type_invoice: true,
            payment_destinations: [],
            loading_search: false,
            type_documents: []
        };
    },
    created() {
        this.initForm();
        this.initDocument();
    },
    methods: {
        initForm() {

            this.errors = {};
            this.form = {
                id: null,
                external_id: null,
                identifier: null,
                date_of_issue: null,
                has_document: false,
                quotation: null
            };
        },
        changeCustomer() {
            this.validateIdentityDocumentType();
        },
        searchRemoteCustomers(input) {
            if (input.length > 0) {
                this.loading_search = true;
                let parameters = `input=${input}&document_type_id=${this.form.document_type_id}&operation_type_id=${this.form.operation_type_id}`;

                this.$http
                    .get(`/${this.resource}/search/customers?${parameters}`)
                    .then(response => {
                        this.customers = response.data.customers;
                        this.loading_search = false;
                    });
            }
        },
        initDocument() {
            this.document = {

                customer_id: null,
                type_document_id: 1,
                currency_id: null,
                date_issue: moment().format('YYYY-MM-DD'),
                date_expiration: null,
                type_invoice_id: 1,
                total_discount: 0,
                total_tax: 0,
                subtotal: 0,
                items: [],
                taxes: [],
                total: 0,
                sale: 0,
                time_days_credit: 0,
                service_invoice: {},
                payment_form_id: 1,
                payment_method_id: 1,

            };
        },
        changeDateOfIssue() {
            this.document.date_expiration = this.document.date_of_issue
        },
        resetDocument() {
            this.generate = this.showGenerate ? true : false;
            this.initDocument();
            this.document.type_invoice_id = (this.type_documents.length > 0) ? this.type_documents[0].id : null
            this.changeDocumentType();
        },
        async submit() {
            this.loading_submit = true;
            await this.assignDocument();

            if (this.document.document_type_id === "nv") {
                this.document.prefix = "NV";
                this.resource_documents = "sale-notes";
            } else {
                this.document.prefix = null;
                this.resource_documents = "co-documents";
            }

            this.$http
                .post(`/${this.resource_documents}`, this.document)
                .then(response => {
                    if (response.data.success) {
                        this.documentNewId = response.data.data.id;

                        this.$http.get(`/${this.resource}/changed/${this.form.id}`).then(() => {
                            this.$eventHub.$emit('reloadData');
                        });
                        // console.log(this.document.document_type_id)
                        if (this.document.document_type_id === "nv") {
                            this.showDialogSaleNoteOptions = true;
                        } else {
                            this.showDialogDocumentOptions = true;
                        }

                        this.$eventHub.$emit("reloadData");
                        this.resetDocument();
                        this.getRecord()
                        // this.document.customer_id = this.form.quotation.customer_id;
                        // this.changeCustomer();
                    } else {

                        //this.$message.error(response.data.message);

                        if (response.data.errors) {

                            const mhtl = this.parseMesaageError(response.data.errors)
                            this.$message({
                                duration: 6000,
                                type: 'error',
                                dangerouslyUseHTMLString: true,
                                message: mhtl
                            });
                        }

                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        this.$message.error(error.response.data.message);
                    }
                })
                .then(() => {
                    this.loading_submit = false;
                });
        },
        parseMesaageError(errors) {
            let ht = `Validación de datos <br><br> <ul>`
            for (var key in errors) {
                //var value = objects[key];
                ht += `<li>${key}: ${errors[key][0]}</li>`
            }

            ht += `</ul>`

            return ht
        },
        async assignDocument() {
            let q = this.form.quotation;

            this.document.date_issue = moment().format('YYYY-MM-DD') //q.date_of_issue
            this.document.customer_id = q.customer_id
            this.document.customer = q.customer
            this.document.currency_id = q.currency_id
            this.document.purchase_order = null
            this.document.total_discount = q.total_discount
            this.document.total_tax = q.total_tax
            this.document.subtotal = q.subtotal
            this.document.total = q.total
            this.document.sale = q.sale
            this.document.items = q.items
            this.document.taxes = q.taxes
            this.document.payments = q.payments;

            await this.document.items.forEach((it) => {
                // it.id = it.item_id
                it.price = it.unit_price
            })

            this.document.service_invoice = await this.createInvoiceService();
            this.document.quotation_id = this.form.id;
        },

        async create() {
            await this.$http.get(`/${this.resource}/option/tables`).then(response => {
                this.type_documents = response.data.type_documents;
            });

            await this.$http
                .get(`/${this.resource}/record2/${this.recordId}`)
                .then(response => {
                    this.form = response.data.data;
                    // this.document.payments = response.data.data.quotation.payments;
                    // this.getCustomer();
                    let type = this.type == "edit" ? "editada" : "registrada";
                    this.titleDialog = `Cotización ${type}: ` + this.form.identifier;
                });
        },
        async getRecord() {

            await this.$http
                .get(`/${this.resource}/record2/${this.recordId}`)
                .then(response => {
                    this.form = response.data.data;
                    let type = this.type == "edit" ? "editada" : "registrada";
                    this.titleDialog = `Cotización ${type}: ` + this.form.identifier;
                });

        }, 
        filterSeries() {
            this.document.series_id = null;
            this.series = _.filter(this.all_series, {
                document_type_id: this.document.document_type_id
            });
            this.document.series_id =
                this.series.length > 0 ? this.series[0].id : null;
        },
        clickFinalize() {
            location.href = `/${this.resource}`;
        },
        clickNewQuotation() {
            this.clickClose();
        },
        clickClose() {
            this.$emit("update:showDialog", false);
            this.initForm();
            this.resetDocument();
        },
        clickToPrint(format) {
            window.open(
                `/${this.resource}/print/${this.form.external_id}/${format}`,
                "_blank"
            );
        },
        clickSendEmail() {
            this.loading = true;
            this.$http
                .post(`/${this.resource}/email`, {
                    customer_email: this.customer_email,
                    id: this.form.id,
                    customer_id: this.form.quotation.customer_id
                })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success("El correo fue enviado satisfactoriamente");
                    } else {
                        this.$message.error("Error al enviar el correo");
                    }
                })
                .catch(error => {
                    this.$message.error("Error al enviar el correo");
                })
                .then(() => {
                    this.loading = false;
                });
        },

        async createInvoiceService() {
            // let resol = this.resolution.resolution; //TODO
            const invoice = {
                number: 0,
                type_document_id: 1
            };

            invoice.customer = await this.getCustomer();
            invoice.tax_totals = await this.getTaxTotal();
            invoice.legal_monetary_totals = await this.getLegacyMonetaryTotal();
            invoice.allowance_charges = await this.createAllowanceCharge(invoice.legal_monetary_totals.allowance_total_amount, invoice.legal_monetary_totals.line_extension_amount);

            invoice.invoice_lines = await this.getInvoiceLines();
            invoice.with_holding_tax_total = await this.getWithHolding();

            return invoice;
        },
        getCustomer() {

            let customer = this.document.customer
            // let customer = this.customers.find(x => x.id == this.document.customer_id);

            let obj = {
                identification_number: customer.number,
                name: customer.name,
                phone: customer.telephone,
                address: customer.address,
                email: customer.email,
                merchant_registration: "000000"
            };

            // this.document.customer_id = customer.id

            if (customer.type_person_id == 1) { //persona juridica
                obj.dv = customer.dv;
            }

            return obj;
        },

        getTaxTotal() {

            let tax = [];
            this.document.items.forEach(element => {
                let find = tax.find(x => x.tax_id == element.tax.type_tax_id && x.percent == element.tax.rate);
                if (find) {
                    let indexobj = tax.findIndex(x => x.tax_id == element.tax.type_tax_id && x.percent == element.tax.rate);
                    tax.splice(indexobj, 1);
                    tax.push({
                        tax_id: find.tax_id,
                        tax_amount: this.cadenaDecimales(Number(find.tax_amount) + Number(element.total_tax)),
                        percent: this.cadenaDecimales(find.percent),
                        taxable_amount: this.cadenaDecimales(Number(find.taxable_amount) + Number(element.unit_price) * Number(element.quantity)) - Number(element.discount)
                    });
                } else {
                    tax.push({
                        tax_id: element.tax.type_tax_id,
                        tax_amount: this.cadenaDecimales(Number(element.total_tax)),
                        percent: this.cadenaDecimales(Number(element.tax.rate)),
                        taxable_amount: this.cadenaDecimales((Number(element.unit_price) * Number(element.quantity)) - Number(element.discount))
                    });
                }
            });
            //      console.log(tax);
            this.tax_amount_calculate = tax;
            return tax;
        },

        getLegacyMonetaryTotal() {

            let line_ext_am = 0;
            let tax_incl_am = 0;
            let allowance_total_amount = 0;
            this.document.items.forEach(element => {
                line_ext_am += (Number(element.unit_price) * Number(element.quantity)) - Number(element.discount);
                allowance_total_amount += Number(element.discount);
            });

            let total_tax_amount = 0;
            this.tax_amount_calculate.forEach(element => {
                total_tax_amount += Number(element.tax_amount);
            });

            tax_incl_am = line_ext_am + total_tax_amount;

            return {
                line_extension_amount: this.cadenaDecimales(line_ext_am),
                tax_exclusive_amount: this.cadenaDecimales(line_ext_am),
                tax_inclusive_amount: this.cadenaDecimales(tax_incl_am),
                allowance_total_amount: this.cadenaDecimales(allowance_total_amount),
                charge_total_amount: "0.00",
                payable_amount: this.cadenaDecimales(tax_incl_am - allowance_total_amount)
            };

        },

        getInvoiceLines() {

            let data = this.document.items.map(x => {
                return {

                    unit_measure_id: x.item.unit_type.code, //codigo api dian de unidad
                    invoiced_quantity: x.quantity,
                    line_extension_amount: this.cadenaDecimales((Number(x.unit_price) * Number(x.quantity)) - x.discount),
                    free_of_charge_indicator: false,
                    allowance_charges: [{
                        charge_indicator: false,
                        allowance_charge_reason: "DESCUENTO GENERAL",
                        amount: this.cadenaDecimales(x.discount),
                        base_amount: this.cadenaDecimales(Number(x.unit_price) * Number(x.quantity))
                    }],
                    tax_totals: [{
                        tax_id: x.tax.type_tax_id,
                        tax_amount: this.cadenaDecimales(x.total_tax),
                        taxable_amount: this.cadenaDecimales((Number(x.unit_price) * Number(x.quantity)) - x.discount),
                        percent: this.cadenaDecimales(x.tax.rate)
                    }],
                    description: x.item.name,
                    code: x.item.internal_id,
                    type_item_identification_id: 4,
                    price_amount: this.cadenaDecimales(x.unit_price),
                    base_quantity: x.quantity
                };

            });

            return data;
        },

        getWithHolding() {

            let total = this.document.sale
            let list = this.document.taxes.filter(function (x) {
                return x.is_retention && x.apply;
            });

            return list.map(x => {
                return {
                    tax_id: x.type_tax_id,
                    tax_amount: this.cadenaDecimales(x.retention),
                    percent: this.cadenaDecimales(x.rate),
                    taxable_amount: this.cadenaDecimales(total),
                };
            });

        },

        createAllowanceCharge(amount, base) {
            return [{
                discount_id: 1,
                charge_indicator: false,
                allowance_charge_reason: "DESCUENTO GENERAL",
                amount: this.cadenaDecimales(amount),
                base_amount: this.cadenaDecimales(base)
            }]
        },

        cadenaDecimales(amount) {
            if (amount.toString().indexOf(".") != -1)
                return amount.toString();
            else
                return amount.toString() + ".00";
        },
    }

}
</script>
