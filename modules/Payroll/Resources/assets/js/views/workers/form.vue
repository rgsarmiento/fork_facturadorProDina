<template>
    <el-dialog width="60%" :title="titleDialog" :visible="showDialog" @close="close" @open="create" @opened="opened" :close-on-click-modal="false">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.type_worker_id}">
                            <label class="control-label">Tipo de empleado</label>
                            <el-select v-model="form.type_worker_id" filterable>
                                <el-option v-for="option in type_workers" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.type_worker_id" v-text="errors.type_worker_id[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.sub_type_worker_id}">
                            <label class="control-label">Subtipo de empleado</label>
                            <el-select v-model="form.sub_type_worker_id" filterable>
                                <el-option v-for="option in sub_type_workers" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.sub_type_worker_id" v-text="errors.sub_type_worker_id[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.payroll_type_document_identification_id}">
                            <label class="control-label">Tipo de identificación</label>
                            <el-select v-model="form.payroll_type_document_identification_id"  filterable>
                                <el-option v-for="option in payroll_type_document_identifications" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.payroll_type_document_identification_id" v-text="errors.payroll_type_document_identification_id[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.municipality_id}">
                            <label class="control-label">Municipalidad</label>
                            <el-select v-model="form.municipality_id"  filterable>
                                <el-option v-for="option in municipalities" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.municipality_id" v-text="errors.municipality_id[0]"></small>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.code}">
                            <label class="control-label">Código</label>
                            <el-input v-model="form.code">
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.code" v-text="errors.code[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.identification_number}">
                            <label class="control-label">N° Identificación  </label>
                            <el-input v-model="form.identification_number">
                                <!-- <el-button type="primary" slot="append" :loading="loading_search" icon="el-icon-search" @click.prevent="changeNumberIdentification">
                                </el-button> -->
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.identification_number" v-text="errors.identification_number[0]"></small>
                        </div>
                    </div>

                     <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.first_name}">
                            <label class="control-label">Nombre  </label>
                            <el-input v-model="form.first_name" ></el-input>
                            <small class="form-control-feedback" v-if="errors.first_name" v-text="errors.first_name[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.surname}">
                            <label class="control-label">Primer Apellido  </label>
                            <el-input v-model="form.surname" ></el-input>
                            <small class="form-control-feedback" v-if="errors.surname" v-text="errors.surname[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.second_surname}">
                            <label class="control-label">Segundo Apellido  </label>
                            <el-input v-model="form.second_surname" ></el-input>
                            <small class="form-control-feedback" v-if="errors.second_surname" v-text="errors.second_surname[0]"></small>
                        </div>
                    </div>

                     <div class="col-md-8">
                        <div class="form-group" :class="{'has-danger': errors.address}">
                            <label class="control-label">Dirección</label>
                            <el-input v-model="form.address" dusk="address"></el-input>
                            <small class="form-control-feedback" v-if="errors.address" v-text="errors.address[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.type_contract_id}">
                            <label class="control-label">Tipo contrato</label>
                            <el-select v-model="form.type_contract_id" filterable>
                                <el-option v-for="option in type_contracts" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.type_contract_id" v-text="errors.type_contract_id[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.salary}">
                            <label class="control-label">Salario</label>
                            <el-input v-model="form.salary"></el-input>
                            <small class="form-control-feedback" v-if="errors.salary" v-text="errors.salary[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3 mt-4">
                        <div class="form-group" :class="{'has-danger': errors.high_risk_pension}">
                            <el-checkbox v-model="form.high_risk_pension">Pensión de alto riesgo</el-checkbox>
                        </div>
                    </div>

                    <div class="col-md-3 mt-4">
                        <div class="form-group" :class="{'has-danger': errors.integral_salarary}">
                            <el-checkbox v-model="form.integral_salarary">Salario integral</el-checkbox>
                        </div>
                    </div>
 
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

    // import { calcularDv } from '../../../functions/Nit';


    // import Helper from '@assetsModuleProColombia/mixins/Helper';

    export default {
        // mixins: [Helper],
        props: ['showDialog', 'type', 'recordId', 'external', 'document_type_id','input_person'],
        data() {
            return {
                loading_submit: false,
                titleDialog: null,
                resource: 'payroll/workers',
                errors: {},
                form: {},
                type_workers: [],
                sub_type_workers: [],
                payroll_type_document_identifications: [],
                type_contracts: [],
                municipalities: [], 
                loading_search: false,
            }
        },
        async created() {
            await this.initForm()

            await this.$http.get(`/${this.resource}/tables`)
                .then(response => { 

                    this.type_workers = response.data.type_workers 
                    this.sub_type_workers = response.data.sub_type_workers 
                    this.payroll_type_document_identifications = response.data.payroll_type_document_identifications 
                    this.type_contracts = response.data.type_contracts 
                    this.municipalities = response.data.municipalities 

                })
        },
        computed: { 
        },
        methods: {
            initForm() {

                this.errors = {}

                this.form = {
                    id: null,
                    code: null,
                    type_worker_id: null,
                    sub_type_worker_id: null,
                    payroll_type_document_identification_id: null,
                    municipality_id: null,
                    type_contract_id: null,
                    identification_number: null,
                    surname: null,
                    second_surname: null,
                    first_name: null,
                    address: null,
                    high_risk_pension: false,
                    integral_salarary: false,
                    salary: null
                }

            }, 
            async opened() {

            },
            create() {

                this.titleDialog = this.recordId ? 'Editar empleado' : 'Nuevo empleado'
                this.getRecord()
            }, 
            getRecord(){

                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                        })
                }
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            if (this.external) {
                                this.$eventHub.$emit('reloadDataPersons', response.data.id)
                            } else {
                                this.$eventHub.$emit('reloadData')
                            }
                            this.close()
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
                        this.loading_submit = false
                    })
            }, 
            close() {
                this.$eventHub.$emit('initInputPerson')
                this.$emit('update:showDialog', false)
                this.initForm()
            }, 
        }
    }
</script>
