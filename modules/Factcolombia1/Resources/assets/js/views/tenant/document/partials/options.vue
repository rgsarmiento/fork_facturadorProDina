<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" append-to-body>
       
        <div class="row mb-4" v-if="form.response_api_message">
            <div class="col-md-12">
                <el-alert
                    :title="form.response_api_message"
                    type="success"
                    show-icon>
                </el-alert>
            </div>
        </div>   

        <div class="row" v-if="showDownload">
 
            <div class="col-lg-6 col-md-6 col-sm-12 text-center font-weight-bold mt-3">
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownload(form.download_pdf)">
                    <i class="fa fa-file-pdf"></i>
                </button>
                <p>Descargar PDF</p>
            </div>
             <div class="col-lg-6 col-md-6 col-sm-12 text-center font-weight-bold mt-3">
               
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownload(form.download_xml)">
                    <i class="fa fa-file-excel"></i>
                </button>
                 <p>Descargar XML</p>
            </div>
 
        </div> 
        <div class="row mt-3">
            <div class="col-md-12">
                <el-input v-model="form.customer_email">
                    <el-button slot="append" icon="el-icon-message" @click="clickSendEmail" :loading="loading">Enviar</el-button>
                </el-input>
                <small class="form-control-feedback" v-if="errors.customer_email" v-text="errors.customer_email[0]"></small>
            </div>
        </div>
        <!-- <div class="row mt-3">
            <div class="col-md-12">
                <el-input v-model="form.customer_phone">
                    <template slot="prepend">+51</template>
                        <el-button slot="append" @click="clickSendWhatsapp" >Enviar
                            <el-tooltip class="item" effect="dark"  content="Es necesario tener aperturado Whatsapp web" placement="top-start">
                                <i class="fab fa-whatsapp" ></i>
                            </el-tooltip>
                        </el-button>
                </el-input>
                <small class="form-control-feedback" v-if="errors.customer_phone" v-text="errors.customer_phone[0]"></small>
            </div>
        </div>  -->
        <span slot="footer" class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
            </template>
            <template v-else>
                <el-button class="list" @click="clickFinalize">Ir al listado</el-button>
                <el-button type="primary" @click="clickNewDocument">Nuevo comprobante</el-button>
            </template>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'recordId', 'showClose', 'showDownload'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'co-documents',
                errors: {},
                form: {},
                company: {},
                locked_emission:{}
            }
        },
        async created() {
            this.initForm()
            await this.$http.get(`/companies/record`)
                .then(response => {
                    if (response.data !== '') {
                        this.company = response.data.data
                    }
                })
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
            initForm() {
                this.errors = {};
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
                };
            },
            async create() {
                await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    this.form = response.data.data;
                    this.titleDialog = 'Comprobante: '+this.form.number_full;
                });
 
            },
            clickPrint(format){
                window.open(`/print/document/${this.form.external_id}/${format}`, '_blank');
            },  
            clickSendEmail() {
                this.loading = true
                this.$http.post(`/${this.resource}/sendEmail`, {
                    email: this.form.customer_email,
                    number: this.form.correlative_api
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
            clickFinalize() {
                location.href = (this.isContingency) ? `/contingencies` : `/${this.resource}`
            },
            clickNewDocument() {
                this.clickClose()
            },
            clickClose() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>
