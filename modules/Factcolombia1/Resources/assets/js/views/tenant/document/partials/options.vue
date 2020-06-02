<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" append-to-body>
             
        <!-- <div class="row">
 
            <div class="col-lg-3 col-md-3 col-sm-12 text-center font-weight-bold mt-3">
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickPrint('a4')">
                    <i class="fa fa-file-alt"></i>
                </button>
                <p>Imprimir A4</p>
            </div>
             <div class="col-lg-3 col-md-3 col-sm-12 text-center font-weight-bold mt-3">
               
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickPrint('ticket')">
                    <i class="fa fa-receipt"></i>
                </button>
                 <p>Imprimir Ticket 80MM</p>
            </div>
 
        </div>  -->
        <div class="row mt-3">
            <div class="col-md-12">
                <el-input v-model="form.client_email">
                    <el-button slot="append" icon="el-icon-message" @click="clickSendEmail" :loading="loading">Enviar</el-button>
                </el-input>
                <small class="form-control-feedback" v-if="errors.client_email" v-text="errors.client_email[0]"></small>
            </div>
        </div>
        <!-- <div class="row mt-3">
            <div class="col-md-12">
                <el-input v-model="form.client_phone">
                    <template slot="prepend">+51</template>
                        <el-button slot="append" @click="clickSendWhatsapp" >Enviar
                            <el-tooltip class="item" effect="dark"  content="Es necesario tener aperturado Whatsapp web" placement="top-start">
                                <i class="fab fa-whatsapp" ></i>
                            </el-tooltip>
                        </el-button>
                </el-input>
                <small class="form-control-feedback" v-if="errors.client_phone" v-text="errors.client_phone[0]"></small>
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
        props: ['showDialog', 'recordId', 'showClose'],
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
            clickSendWhatsapp() {
                
                if(!this.form.client_phone){
                    return this.$message.error('El número es obligatorio')
                }

                window.open(`https://wa.me/51${this.form.client_phone}?text=${this.form.message_text}`, '_blank');
            
            },
            initForm() {
                this.errors = {};
                this.form = {
                    id: null,
                    number_full:null,
                    client_email:null,
                    client_phone:null,
                    correlative_api:null,
                    message_text: null
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
            clickDownload(format) {
                window.open(`${this.form.download_pdf}/${format}`, '_blank');
            },
            clickSendEmail() {
                this.loading = true
                this.$http.post(`/${this.resource}/sendEmail`, {
                    email: this.form.client_email,
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
