<template>
    <el-dialog   :visible="showDialog"  @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
        <span slot="title">
            <div class="widget-summary widget-summary-xs pl-3 p-2">
                <div class="widget-summary-col widget-summary-col-icon">
                    <div class="summary-icon bg-success">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <div class="widget-summary-col">
                    <div class="summary row">
                        <div class="col-md-12">
                            <h4 class="title">Venta exitosa : comprobante {{form.number_full}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </span>
        <div class="form-body el-dialog__body_custom">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 text-center font-weight-bold mt-5">
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownload(form.download_pdf)">
                        <i class="fa fa-file-pdf"></i>
                    </button>
                    <p>Descargar PDF</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 text-center font-weight-bold mt-5">

                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownload(form.download_xml)">
                        <i class="fa fa-file-excel"></i>
                    </button>
                    <p>Descargar XML</p>
                </div>
                <div class="row col-md-12 mt-5">
                    <div class="col-md-8">
                        <el-input v-model="form.customer_email">
                            <el-button slot="append" icon="el-icon-message"   @click="clickSendEmail" :loading="loading">Enviar</el-button>
                        </el-input>
                        <!-- <small class="form-control-feedback" v-if="errors.customer_email" v-text="errors.customer_email[0]"></small> -->

                    </div>
                    <!-- <div class="col-md-1">
                    </div> -->
                    <div class="col-md-4">
                        <el-button  type="primary"  class="float-right" @click="clickNewSale">Nueva venta</el-button>
                    </div>
                </div>

            </div>
        </div>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'recordId', 'statusDocument','resource'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                errors: {},
                form: {},
                company: {},
                configuration: {},
                activeName: 'first',

            }
        },
        async created() {
            this.initForm()
        },
        methods: {
            clickDownload(download) {
                window.open(download, '_blank');
            },
            clickSendWhatsapp() {

                if(!this.form.customer_phone){
                    return this.$message.error('El número es obligatorio')
                }

                window.open(`https://wa.me/51${this.form.customer_phone}?text=${this.form.message_text}`, '_blank');

            },
            clickNewSale(){
                this.initForm()
                this.$eventHub.$emit('cancelSale')

            },
            initForm() {
                this.errors = {};
                this.configuration = {};
                this.form = {
                    id: null,
                    number_full:null,
                    customer_email:null,
                    customer_phone:null,
                    correlative_api:null,
                    message_text: null,
                    response_api_message: null,
                    download_pdf: null,
                    download_xml: null,
                }
            },
            create() {
                this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    this.form = response.data.data;
                    this.titleDialog = 'Comprobante: '+this.form.number;
                });

                // this.$http.get(`/pos/status_configuration`).then(response => {
                //     this.configuration = response.data
                // });
            },
            clickSendEmail() {

                if(this.form.customer_email == null || this.form.customer_email == '') return this.$message.error('Ingrese el correo')
                this.loading = true
                this.$http.post(`/${this.resource}/sendEmail`, {
                    email: this.form.customer_email,
                    number: this.form.correlative_api,
                    number_full: this.form.number_full
                })
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success('El correo fue enviado satisfactoriamente')
                        } else {
                            this.$message.error('Error al enviar el correo')
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
                        } else {
                            this.$message.error(error.response.data.message)
                        }
                    })
                    .then(() => {
                        this.loading = false
                    })
            },
            // clickConsultCdr(document_id) {
            //     this.$http.get(`/${this.resource}/consult_cdr/${document_id}`)
            //         .then(response => {
            //             if (response.data.success) {
            //                 this.$message.success(response.data.message)
            //                 this.$eventHub.$emit('reloadData')
            //             } else {
            //                 this.$message.error(response.data.message)
            //             }
            //         })
            //         .catch(error => {
            //             this.$message.error(error.response.data.message)
            //         })
            // },
            // clickFinalize() {
            //     location.href = (this.isContingency) ? `/contingencies` : `/${this.resource}`
            // },
            // clickNewDocument() {
            //     this.clickClose()
            // },
            // clickClose() {
            //     this.$emit('update:showDialog', false)
            //     this.initForm()
            // },
        }
    }
</script>
