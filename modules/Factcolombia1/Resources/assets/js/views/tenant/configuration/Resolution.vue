<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Resolucion de Facturacion</h3>
        </div>
        <div class="tab-content">
            <div class="resolution">
                <form autocomplete="off">
                    <div class="form-body">
                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.type_document_id}">
                                    <label class="control-label">Tipo de Documento *</label>
                                    <el-select
                                        v-model="resolution.type_document_id"
                                        filterable
                                        remote class="border-left rounded-left border-info"
                                        popper-class="el-select-type-document"
                                        placeholder="Seleccione el tipo de documento.">
                                        <el-option
                                            v-for="option in typeDocuments"
                                            :key="option.id"
                                            :value="option.id"
                                            :label="option.name">
                                        </el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.type_document_id" v-text="errors.type_document_id[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.prefix}">
                                    <label class="control-label">Prefijo *</label>
                                    <el-input
                                        v-model="resolution.prefix"
                                        placeholder="Digite el prefijo de la resolucion"
                                        maxlength="4"
                                        :disabled="false">
                                    </el-input>
                                    <small class="form-control-feedback" v-if="errors.prefix" v-text="errors.prefix[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.resolution}">
                                    <label class="control-label">Nro Resolucion *</label>
                                    <el-input
                                        v-model="resolution.resolution"
                                        placeholder="Digite el numero de resolucion."
                                        :disabled="false">
                                    </el-input>
                                    <small class="form-control-feedback" v-if="errors.resolution" v-text="errors.resolution[0]"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.resolution_date}">
                                    <label class="control-label">Fecha Resolucion</label>
                                    <el-date-picker
                                        v-model="resolution.resolution_date"
                                        type="date"
                                        value-format="yyyy-MM-dd"
                                        placeholder="Seleccione la fecha de emision de la resolucion."
                                        :clearable="false">
                                    </el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.resolution_date" v-text="errors.resolution_date[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.date_from}">
                                    <label class="control-label">Fecha Desde</label>
                                    <el-date-picker
                                        v-model="resolution.date_from"
                                        type="date"
                                        value-format="yyyy-MM-dd"
                                        placeholder="Seleccione la fecha inicial de validez de la resolucion."
                                        :clearable="false">
                                    </el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_from" v-text="errors.date_from[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.date_to}">
                                    <label class="control-label">Fecha Hasta</label>
                                    <el-date-picker
                                        v-model="resolution.date_to"
                                        type="date"
                                        value-format="yyyy-MM-dd"
                                        placeholder="Seleccione la fecha final de validez de la resolucion."
                                        :clearable="false">
                                    </el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_to" v-text="errors.date_to[0]"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.from}">
                                    <label class="control-label">Desde *</label>
                                    <el-input
                                        v-model="resolution.from"
                                        placeholder="Introduzca el numero inicial de la resolucion."
                                        :disabled="false">
                                    </el-input>
                                    <small class="form-control-feedback" v-if="errors.from" v-text="errors.from[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.to}">
                                    <label class="control-label">Hasta *</label>
                                    <el-input
                                        v-model="resolution.to"
                                        placeholder="Digite el numero final de la resolucion."
                                        :disabled="false">
                                    </el-input>
                                    <small class="form-control-feedback" v-if="errors.to" v-text="errors.to[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.technical_key}">
                                    <label class="control-label">Clave Tecnica *</label>
                                    <el-input
                                        v-model="resolution.technical_key"
                                        placeholder="Introduzca la clave tecnica para esta resolucion."
                                        :disabled="false">
                                    </el-input>
                                    <small class="form-control-feedback" v-if="errors.technical_key" v-text="errors.technical_key[0]"></small>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions text-right mt-4">
                            <el-button
                                type="primary"
                                :loading="loadingResolution"
                                @click="validateResolution()">Guardar</el-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import Helper from "../../../mixins/Helper";
    export default {
        mixins: [Helper],
        props: {
            route: {
                required: true
            }
        },

        data: () => ({
            typeDocuments: [
                { id: 1, name: "Factura de Venta Nacional" },
                { id: 2, name: "Factura de Exportación" },
                { id: 3, name: "Factura de Contingencia" },
                { id: 4, name: "Nota Crédito" },
                { id: 5, name: "Nota Débito" },
                { id: 6, name: "ZIP" }
            ],
            errors: {
            },
            resolution: {
            },
            loadingResolution: false,
        }),

        mounted() {
            this.errors = {
            }
            if (window.File && window.FileReader && window.FileList && window.Blob)
                console.log("ok.");
            else
                alert("The File APIs are not fully supported in this browser.");
        },

        methods: {
            initForm() {
                this.resolution.type_document_id = '';
                this.resolution.prefix = '';
                this.resolution.resolution = '';
                this.resolution.resolution_date = '';
                this.resolution.date_from = '';
                this.resolution.date_to = '';
                this.resolution.from = '';
                this.resolution.to = '';
                this.resolution.technical_key = '';
            },

            validateResolution(scope, model = null, models = null, modelObject = null) {
                this.loadingResolution = true
                this.$http.post(`/client/configuration/storeServiceCompanieResolution`, this.resolution)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loadingResolution = false
                        this.initForm()
                    })
            },
        }
    };
</script>
