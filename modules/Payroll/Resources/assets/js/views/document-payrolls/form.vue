<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="tab-content" v-if="loading_form">
            <div class="invoice">
                <form autocomplete="off" @submit.prevent="submit">
                    <div class="form-body">
                        <div class="row">
                        </div>
                        <div class="row mt-4">

                            <div class="col-md-6 pb-2">
                                <div class="form-group" :class="{'has-danger': errors.worker_id}">
                                    <label class="control-label font-weight-bold text-info">
                                        Empleados<span class="text-danger"> *</span>
                                        <el-tooltip class="item" effect="dark" content="Escribir al menos 3 caracteres para buscar" placement="top-start">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                        <a href="#" @click.prevent="showDialogNewWorker = true">[+ Nuevo]</a>
                                    </label>
                                    <el-select v-model="form.worker_id" filterable remote class="border-left rounded-left border-info" popper-class="el-select-workers"
                                        placeholder="Escriba el nombre o número de documento del empleado"
                                        :remote-method="searchRemoteWorkers"
                                        :loading="loading_search"
                                        @change="changeWorker">

                                        <el-option v-for="option in workers" :key="option.id" :value="option.id" :label="option.search_fullname"></el-option>

                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.worker_id" v-text="errors.worker_id[0]"></small>
                                </div>
                            </div>
 

                            <div class="col-md-3 pb-2">
                                <div class="form-group" :class="{'has-danger': errors.type_document_id}">
                                    <label class="control-label">Resolución
                                        <span class="text-danger"> *</span>
                                    </label>
                                    <el-select @change="changeResolution" v-model="form.type_document_id" class="border-left rounded-left border-info">
                                        <el-option v-for="option in resolutions" :key="option.id" :value="option.id" :label="`${option.prefix}`"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.type_document_id" v-text="errors.type_document_id[0]"></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" :class="{'has-danger': errors.payroll_period_id}">
                                    <label class="control-label">Periodo de nómina<span class="text-danger"> *</span>
                                        <el-tooltip class="item" effect="dark" content="Frecuencia de pago" placement="top-start">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </label>
                                    <el-select v-model="form.payroll_period_id"   filterable class="border-left rounded-left border-info" :disabled="form_disabled.payroll_period_id">
                                        <el-option v-for="option in payroll_periods" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.payroll_period_id" v-text="errors.payroll_period_id[0]"></small>
                                </div>
                            </div>
 
                        </div>


                        <el-tabs v-model="activeName">
                            <el-tab-pane label="Periodo" name="period">
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['period.admision_date']}">
                                            <label class="control-label">Fecha de admisión<span class="text-danger"> *</span>
                                                <el-tooltip class="item" effect="dark" content="Fecha de inicio de labores del empleado" placement="top-start">
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <el-date-picker v-model="form.period.admision_date" type="date" value-format="yyyy-MM-dd" :clearable="false" :disabled="form_disabled.admision_date"></el-date-picker>
                                            <small class="form-control-feedback" v-if="errors['period.admision_date']" v-text="errors['period.admision_date'][0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['period.settlement_start_date']}">
                                            <label class="control-label">F. Inicio de periodo de liquidación<span class="text-danger"> *</span></label>
                                            <el-date-picker v-model="form.period.settlement_start_date" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changePeriodSettlement"></el-date-picker>
                                            <small class="form-control-feedback" v-if="errors['period.settlement_start_date']" v-text="errors['period.settlement_start_date'][0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['period.settlement_end_date']}">
                                            <label class="control-label">F. Finalización de periodo de liquidación<span class="text-danger"> *</span></label>
                                            <el-date-picker v-model="form.period.settlement_end_date" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changePeriodSettlement"></el-date-picker>
                                            <small class="form-control-feedback" v-if="errors['period.settlement_end_date']" v-text="errors['period.settlement_end_date'][0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['period.issue_date']}">
                                            <label class="control-label">Fecha de emisión<span class="text-danger"> *</span></label>
                                            <el-date-picker v-model="form.period.issue_date" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                            <small class="form-control-feedback" v-if="errors['period.issue_date']" v-text="errors['period.issue_date'][0]"></small>
                                        </div>
                                    </div>
 
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['period.worked_time']}">
                                            <label class="control-label">Tiempo trabajado<span class="text-danger"> *</span></label>
                                            <el-input-number v-model="form.period.worked_time" :min="0" controls-position="right"></el-input-number>
                                            <small class="form-control-feedback" v-if="errors['period.worked_time']" v-text="errors['period.worked_time'][0]"></small>
                                        </div>
                                    </div>
 
                                
                                </div>
                            </el-tab-pane>
                            <el-tab-pane label="Pagos" name="payments">
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['payment.payment_method_id']}">
                                            <label class="control-label">Métodos de pago<span class="text-danger"> *</span></label>
                                            <el-select v-model="form.payment.payment_method_id"   filterable @change="changePaymentMethod" :disabled="form_disabled.payment">
                                                <el-option v-for="option in payment_methods" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                            </el-select>
                                            <small class="form-control-feedback" v-if="errors['payment.payment_method_id']" v-text="errors['payment.payment_method_id'][0]"></small>
                                        </div>
                                    </div>
                                        
                                    <template v-if="show_inputs_payment_method">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-danger': errors['payment.bank_name']}">
                                                <label class="control-label">Nombre del banco</label>
                                                <el-input v-model="form.payment.bank_name" :disabled="form_disabled.payment"></el-input>
                                                <small class="form-control-feedback" v-if="errors['payment.bank_name']" v-text="errors['payment.bank_name'][0]"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-danger': errors['payment.account_type']}">
                                                <label class="control-label">Tipo de cuenta</label>
                                                <el-input v-model="form.payment.account_type" :disabled="form_disabled.payment"></el-input>
                                                <small class="form-control-feedback" v-if="errors['payment.account_type']" v-text="errors['payment.account_type'][0]"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-danger': errors['payment.account_number']}">
                                                <label class="control-label">Número de cuenta</label>
                                                <el-input v-model="form.payment.account_number" :disabled="form_disabled.payment"></el-input>
                                                <small class="form-control-feedback" v-if="errors['payment.account_number']" v-text="errors['payment.account_number'][0]"></small>
                                            </div>
                                        </div>
                                    </template>

                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <div class="form-group" :class="{'has-danger': errors['payment_dates']}">
                                            <h4>Fechas de pago<span class="text-danger"> *</span></h4>
                                            <small class="form-control-feedback" v-if="errors['payment_dates']" v-text="errors['payment_dates'][0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <table>
                                            <thead>
                                                <tr width="100%">
                                                    <th v-if="form.payment_dates.length>0" class="pb-2">Fecha<span class="text-danger"> *</span></th>
                                                    <th width="30%"><a href="#" @click.prevent="clickAddPaymentDate()" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(row, index) in form.payment_dates" :key="index"> 
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-date-picker v-model="row.payment_date" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                                        </div>
                                                    </td>
                                                    <td class="series-table-actions text-center">
                                                        <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelPaymentDate(index)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                    <br>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </el-tab-pane>
                            <el-tab-pane label="Devengados" name="accrued">
                                
                                <div class="row"> 
                                    
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['accrued.worked_days']}">
                                            <label class="control-label">Días trabajados<span class="text-danger"> *</span></label>
                                            <el-input-number v-model="form.accrued.worked_days" :min="1" :max="30" :precision="0" controls-position="right" @change="changeWorkedDays"></el-input-number>
                                            <small class="form-control-feedback" v-if="errors['accrued.worked_days']" v-text="errors['accrued.worked_days'][0]"></small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['accrued.salary']}">
                                            <label class="control-label">Salario<span class="text-danger"> *</span></label>
                                            <el-input-number v-model="form.accrued.salary" :min="0" controls-position="right" disabled></el-input-number>
                                            <!-- <el-input-number v-model="form.accrued.salary" :min="0" controls-position="right" @change="changeAccruedSalary"></el-input-number> -->
                                            <small class="form-control-feedback" v-if="errors['accrued.salary']" v-text="errors['accrued.salary'][0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['accrued.transportation_allowance']}">
                                            <label class="control-label">Subsidio de transporte</label>
                                            <el-input-number v-model="form.accrued.transportation_allowance" :min="0" disabled controls-position="right"></el-input-number>
                                            <small class="form-control-feedback" v-if="errors['accrued.transportation_allowance']" v-text="errors['accrued.transportation_allowance'][0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['accrued.telecommuting']}">
                                            <label class="control-label">Teletrabajo</label>
                                            <el-input-number v-model="form.accrued.telecommuting" :min="0" controls-position="right"></el-input-number>
                                            <small class="form-control-feedback" v-if="errors['accrued.telecommuting']" v-text="errors['accrued.telecommuting'][0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['accrued.accrued_total']}">
                                            <label class="control-label">Total devengados<span class="text-danger"> *</span></label>
                                            <el-input-number v-model="form.accrued.accrued_total" :min="0" controls-position="right" ></el-input-number>
                                            <small class="form-control-feedback" v-if="errors['accrued.accrued_total']" v-text="errors['accrued.accrued_total'][0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-md waves-effect waves-light btn-primary" @click.prevent="clickAddExtraHours">Agregar Horas Extras</button>
                                        </div>
                                    </div>
                                </div>  


                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <div class="form-group" :class="{'has-danger': errors['accrued.work_disabilities']}">
                                            <h4>Incapacidades</h4>
                                            <small class="form-control-feedback" v-if="errors['accrued.work_disabilities']" v-text="errors['accrued.work_disabilities'][0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <table>
                                            <thead>
                                                <tr width="100%">
                                                    <template v-if="form.accrued.work_disabilities.length>0">
                                                        <th class="pb-2">Fecha inicio</th>
                                                        <th class="pb-2">Fecha término</th>
                                                        <th class="pb-2">Tipo</th>
                                                        <th class="pb-2">Cantidad</th>
                                                        <th class="pb-2">Pago</th>
                                                    </template>
                                                    <th width="10%"><a href="#" @click.prevent="clickAddWorkDisability" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(row, index) in form.accrued.work_disabilities" :key="index"> 
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-date-picker v-model="row.start_date" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-date-picker v-model="row.end_date" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-select v-model="row.type" filterable>
                                                                <el-option v-for="option in type_disabilities" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                                            </el-select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-input-number v-model="row.quantity" :min="0" controls-position="right"></el-input-number>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-input-number v-model="row.payment" :min="0" controls-position="right"></el-input-number>
                                                        </div>
                                                    </td>

                                                    <td class="series-table-actions text-center">
                                                        <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelWorkDisability(index)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                    <br>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </el-tab-pane>
                            <el-tab-pane label="Deducciones" name="deduction">
                                
                                <div class="row"> 
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['deduction.eps_type_law_deductions_id']}">
                                            <label class="control-label">EPS - Deducciones por ley
                                                <span class="text-danger"> *</span>
                                            </label>
                                            <el-select v-model="form.deduction.eps_type_law_deductions_id"   filterable @change="changeEpsTypeLawDeduction">
                                                <el-option v-for="option in type_law_deductions" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                            </el-select>
                                            <small class="form-control-feedback" v-if="errors['deduction.eps_type_law_deductions_id']" v-text="errors['deduction.eps_type_law_deductions_id'][0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['deduction.eps_deduction']}">
                                            <label class="control-label">Deducción EPS
                                                <span v-if="getPercentageEpsTypeLawDeduction"> ({{ getPercentageEpsTypeLawDeduction.percentage }}%) </span>
                                                <span class="text-danger"> *</span>
                                            </label>
                                            <el-input-number v-model="form.deduction.eps_deduction" :min="0" controls-position="right" @change="changeEpsDeduction"></el-input-number>
                                            <small class="form-control-feedback" v-if="errors['deduction.eps_deduction']" v-text="errors['deduction.eps_deduction'][0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['deduction.pension_type_law_deductions_id']}">
                                            <label class="control-label">Pensión - Deducciones por ley<span class="text-danger"> *</span></label>
                                            <el-select v-model="form.deduction.pension_type_law_deductions_id"   filterable @change="changePensionTypeLawDeduction">
                                                <el-option v-for="option in type_law_deductions" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                            </el-select>
                                            <small class="form-control-feedback" v-if="errors['deduction.pension_type_law_deductions_id']" v-text="errors['deduction.pension_type_law_deductions_id'][0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['deduction.pension_deduction']}">
                                            <label class="control-label">Deducción de pensión
                                                <span v-if="getPercentagePensionTypeLawDeduction"> ({{ getPercentagePensionTypeLawDeduction.percentage }}%) </span>
                                                <span class="text-danger"> *</span>
                                            </label>
                                            <el-input-number v-model="form.deduction.pension_deduction" :min="0" controls-position="right" @change="changePensionDeduction"></el-input-number>
                                            <small class="form-control-feedback" v-if="errors['deduction.pension_deduction']" v-text="errors['deduction.pension_deduction'][0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">AFC</label>
                                            <el-input-number v-model="form.deduction.afc" :min="0" controls-position="right"></el-input-number>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Reintegro</label>
                                            <el-input-number v-model="form.deduction.refund" :min="0" controls-position="right"></el-input-number>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Deuda</label>
                                            <el-input-number v-model="form.deduction.debt" :min="0" controls-position="right"></el-input-number>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Educación</label>
                                            <el-input-number v-model="form.deduction.education" :min="0" controls-position="right"></el-input-number>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-danger': errors['deduction.deductions_total']}">
                                            <label class="control-label">Deducción Total<span class="text-danger"> *</span></label>
                                            <el-input-number v-model="form.deduction.deductions_total" :min="0" controls-position="right"></el-input-number>
                                            <small class="form-control-feedback" v-if="errors['deduction.deductions_total']" v-text="errors['deduction.deductions_total'][0]"></small>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h4>Sindicatos</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h4>Sanciones</h4>
                                        </div>
                                    </div>

                                    <!-- Sindicatos -->
                                    <div class="col-md-6">
                                        <table>
                                            <thead>
                                                <tr width="100%">
                                                    <template v-if="form.deduction.labor_union.length>0">
                                                        <th class="pb-2">Porcentaje</th>
                                                        <th class="pb-2">Deducción</th>
                                                    </template>
                                                    <th width="15%"><a href="#" @click.prevent="clickAddLaborUnion" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(row, index) in form.deduction.labor_union" :key="index"> 
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-input-number v-model="row.percentage" :min="0" controls-position="right"></el-input-number>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-input-number v-model="row.deduction" :min="0" controls-position="right"></el-input-number>
                                                        </div>
                                                    </td>
                                                    <td class="series-table-actions text-center">
                                                        <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelLaborUnion(index)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                    <br>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Sindicatos -->
                                    
                                    <!-- Sanciones -->
                                    <div class="col-md-6">
                                        <table>
                                            <thead>
                                                <tr width="100%">
                                                    <template v-if="form.deduction.sanctions.length>0">
                                                        <th class="pb-2">Sanción pública</th>
                                                        <th class="pb-2">Sanción privada</th>
                                                    </template>
                                                    <th width="15%"><a href="#" @click.prevent="clickAddSanction" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(row, index) in form.deduction.sanctions" :key="index"> 
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-input-number v-model="row.public_sanction" :min="0" controls-position="right"></el-input-number>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-2 mr-2"  >
                                                            <el-input-number v-model="row.private_sanction" :min="0" controls-position="right"></el-input-number>
                                                        </div>
                                                    </td>
                                                    <td class="series-table-actions text-center">
                                                        <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelSanction(index)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                    <br>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Sanciones -->

                                </div>

                            </el-tab-pane>
                        </el-tabs>
 
                    </div>


                    <div class="form-actions text-right mt-4">
                        <el-button @click.prevent="close()">Cancelar</el-button>
                        <el-button class="submit" type="primary" native-type="submit" :loading="loading_submit" >Generar</el-button>
                    </div>
                </form>
            </div>

            <worker-form :showDialog.sync="showDialogNewWorker"
                        :external="true"></worker-form>
 

            <document-payroll-options :showDialog.sync="showDialogDocumentPayrollOptions"     
                            :recordId="recordId"
                            :showDownload="true"
                            :showClose="false">
                            </document-payroll-options>
                            
            <document-payroll-extra-hours :showDialog.sync="showDialogDocumentPayrollExtraHours"     
                            :form="form"
                            :errors="errors"
                            @sumTotalsExtraHoursForm="sumTotalsExtraHoursForm"
                            ref="componentDocumentPayrollExtraHours"
                            >
                            </document-payroll-extra-hours>
        </div>
    </div>
</template>
 
<script> 

    import WorkerForm from '../workers/form.vue' 
    import DocumentPayrollOptions from './partials/options.vue' 
    import DocumentPayrollExtraHours from './partials/extra_hours.vue' 

    export default {
        components: {WorkerForm, DocumentPayrollOptions, DocumentPayrollExtraHours},
        data() {
            return {
                resource: 'payroll/document-payrolls',
                loading_submit: false,
                loading_form: false,
                loading_search: false,
                errors: {},
                form: {},
                all_workers: [],
                workers: [],
                payroll_periods: [],
                payment_methods: [], 
                type_law_deductions: [], 
                type_documents: [], 
                resolutions:[],
                type_disabilities: [],
                showDialogNewWorker: false,
                activeName: 'period',
                recordId:null,
                showDialogDocumentPayrollOptions:false,
                form_disabled: {},
                show_inputs_payment_method: true,
                advanced_configuration: {},
                showDialogDocumentPayrollExtraHours: false,
                quantity_days_month: 30
            }
        }, 
        async created() {

            await this.initForm()
            await this.getTables()
            await this.events()

            this.loading_form = true
        }, 
        computed: {
            getPercentageEpsTypeLawDeduction: function () {
                return this.getTypeLawDeduction(this.form.deduction.eps_type_law_deductions_id)
            },
            getPercentagePensionTypeLawDeduction: function () {
                return this.getTypeLawDeduction(this.form.deduction.pension_type_law_deductions_id)
            },
        },
        methods: { 
            getValueFromInputUndefined(value){
                return value ? value : 0 //obtener el valor de un input que puede ser un campo undefined
            },
            getTypeLawDeduction(id){
                return _.find(this.type_law_deductions, { id : id })
            },
            percentageToFactor(percentage){
                return percentage / 100
            },
            getTotalLawDeduction(type_law_deductions_id){

                let type_law_deduction = this.getTypeLawDeduction(type_law_deductions_id)

                // total devengados – subsidio transporte *  % deducciones por ley (type law deductions)
                return _.round((this.form.accrued.accrued_total - this.getValueFromInputUndefined(this.form.accrued.transportation_allowance)) * this.percentageToFactor(type_law_deduction.percentage), 2)

            },
            changeEpsTypeLawDeduction(){
                this.form.deduction.eps_deduction = this.getTotalLawDeduction(this.form.deduction.eps_type_law_deductions_id)
                this.calculateTotalDeduction()
            },
            changePensionTypeLawDeduction(){
                this.form.deduction.pension_deduction = this.getTotalLawDeduction(this.form.deduction.pension_type_law_deductions_id)
                this.calculateTotalDeduction()
            },
            calculateTotalTypeLawDeduction(){
                //calcular eps y pension
                this.changeEpsTypeLawDeduction()
                this.changePensionTypeLawDeduction()
            },
            clickAddExtraHours(){

                if(parseFloat(this.form.accrued.salary) <= 0 || parseFloat(this.form.accrued.total_base_salary) <= 0){
                    return this.$message.warning('El campo Salario debe ser mayor a 0')
                }

                this.showDialogDocumentPayrollExtraHours = true
            },
            changePaymentMethod(){

                //mostrar campos adicionales, si el metodo de pago es el definido en el arreglo/obligatorio
                this.show_inputs_payment_method = [2,3,4,5,6,7,21,22,30,31,42,45,46,47].includes(this.form.payment.payment_method_id)

            },
            changePeriodSettlement(){

                this.form.period.worked_time = moment(this.form.period.settlement_end_date).diff(moment(this.form.period.settlement_start_date), 'days', true)
                
            },
            setInitialDataPeriod(){

                let last_month = moment().subtract(1, 'months')

                this.form.period.settlement_start_date = last_month.startOf('month').format('YYYY-MM-DD')
                this.form.period.settlement_end_date = last_month.endOf('month').format('YYYY-MM-DD')
                this.form.period.worked_time = this.quantity_days_month

            },
            initForm() {

                this.form = {
                    type_document_id: null,
                    prefix: null,
                    period: {
                        admision_date: moment().format('YYYY-MM-DD'),
                        settlement_start_date: null,
                        settlement_end_date: null,
                        worked_time: 0,
                        issue_date: moment().format('YYYY-MM-DD'),
                    },
                    payroll_period_id: null,
                    worker_id: null,
                    resolution_number: null,
                    payment: {
                        payment_method_id: null,
                        bank_name: null,
                        account_type: null,
                        account_number: null,
                    }, 
                    payment_dates: [], 
                    accrued :{
                        worked_days: this.quantity_days_month, 
                        total_base_salary: 0, //salario base del empleado (equivalente a 30 dias), no se afecta por los dias trabajados
                        salary: 0, 
                        accrued_total: 0,
                        transportation_allowance: undefined, //se usa undefined ya que el componente input-number le asigna 0 al valor null
                        telecommuting: undefined,
                        work_disabilities: [],
                        heds: [],
                        hens: [],
                        hrns: [],
                        heddfs: [],
                        hrddfs: [],
                        hendfs: [],
                        hrndfs: [],
                        total_extra_hours: 0, //usado para controlar los totales de horas extras en vista y sumar al total devengados
                    },
                    deduction: {
                        eps_type_law_deductions_id: 1,
                        eps_deduction: 0, 
                        pension_type_law_deductions_id: 5,
                        pension_deduction: 0, 
                        deductions_total: 0, 
                        afc: undefined, 
                        refund: undefined,
                        debt: undefined,
                        education: undefined,
                        labor_union: [],
                        sanctions: [],
                    },
                }

                this.errors = {}

                
                this.form_disabled = {
                    payroll_period_id : false,
                    admision_date : false,
                    payment : false,
                }

                this.setInitialDataPeriod()
            },
            clickAddLaborUnion(){
                
                this.form.deduction.labor_union.push({
                    percentage :  0,
                    deduction :  0,
                })

            },
            clickCancelLaborUnion(index){
                this.form.deduction.labor_union.splice(index, 1)
            },
            clickAddSanction(){
                
                this.form.deduction.sanctions.push({
                    public_sanction :  0,
                    private_sanction :  0,
                })

            },
            clickCancelSanction(index){
                this.form.deduction.sanctions.splice(index, 1)
            },
            clickAddWorkDisability(){

                this.form.accrued.work_disabilities.push({
                    start_date :  moment().format('YYYY-MM-DD'),
                    end_date :  moment().format('YYYY-MM-DD'),
                    type :  null,
                    quantity :  0,
                    payment :  0,
                })

            },
            clickCancelWorkDisability(index){
                this.form.accrued.work_disabilities.splice(index, 1)
            },
            clickAddPaymentDate(param_payment_date = null) {

                let payment_date = param_payment_date ? param_payment_date : moment().format('YYYY-MM-DD')

                this.form.payment_dates.push({
                    payment_date: payment_date
                })

            },
            clickCancelPaymentDate(index) {
                this.form.payment_dates.splice(index, 1)
            },
            events(){

                this.$eventHub.$on('reloadDataWorkers', (worker_id) => {
                    this.reloadDataWorkers(worker_id)
                })

            },
            async getTables(){

                await this.$http.get(`/${this.resource}/tables`)
                    .then(response => {
                        
                        this.all_workers = response.data.workers
                        this.workers = response.data.workers 
                        
                        this.payroll_periods = response.data.payroll_periods 
                        this.payment_methods = response.data.payment_methods 
                        this.type_law_deductions = response.data.type_law_deductions 
                        // this.type_documents = response.data.type_documents 
                        this.resolutions = response.data.resolutions
                        this.type_disabilities = response.data.type_disabilities
                        this.advanced_configuration = response.data.advanced_configuration

                        this.filterWorkers(); 
                    })
            },
            changeResolution(){

                let resolution = _.find(this.resolutions, {id : this.form.type_document_id})
                if(resolution) {
                    this.form.prefix = resolution.prefix
                    this.form.resolution_number = resolution.resolution_number
                }

            }, 
            searchRemoteWorkers(input) {

                if (input.length > 2) {

                    this.loading_search = true
                    let parameters = `input=${input}`

                    this.$http.get(`/payroll/workers/search?${parameters}`)
                            .then(response => {
                                this.workers = response.data.workers
                                this.loading_search = false

                                if(this.workers.length == 0){
                                    this.filterWorkers()
                                }
                            })
                } else {
                    this.filterWorkers()
                }

            },
            resetForm() {
                this.activeName = 'period'
                this.initForm()
            },  
            filterWorkers() { 
                this.workers = this.all_workers
            }, 
            close() {
                location.href = `/${this.resource}`
            },
            reloadDataWorkers(worker_id) { 
                this.$http.get(`/payroll/workers/search-by-id/${worker_id}`).then((response) => {
                    this.workers = response.data.workers
                    this.form.worker_id = worker_id
                    this.changeWorker()
                })
            },
            changePensionDeduction(){
                // this.calculateTotal()
                this.calculateTotalDeduction()
            },
            changeEpsDeduction(){
                // this.calculateTotal()
                this.calculateTotalDeduction()
            },
            calculateTotal(){
                this.calculateTotalAccrued()
                this.calculateTotalTypeLawDeduction()
                this.calculateTotalDeduction()
            },
            calculateTotalDeduction(){

                this.form.deduction.deductions_total = parseFloat(this.form.deduction.eps_deduction) + parseFloat(this.form.deduction.pension_deduction)

            },
            calculateTotalAccrued(){

                this.form.accrued.accrued_total = parseFloat(this.form.accrued.salary) 
                                                + (this.form.accrued.transportation_allowance ? parseFloat(this.form.accrued.transportation_allowance) : 0)
                                                + this.form.accrued.total_extra_hours

            },
            sumTotalsExtraHoursForm(){
                // this.calculateTotalAccrued()
                this.calculateTotal()
            },
            async changeWorker() { 

                let worker = await _.find(this.workers, {id : this.form.worker_id})

                //autocompletar campos
                await this.autocompleteDataFromWorker(worker)

                await this.calculateTotal()

            },
            autocompleteDataFromWorker(worker){

                this.form.payroll_period_id = worker.payroll_period_id
                this.form_disabled.payroll_period_id = worker.payroll_period_id ? true : false

                this.form.period.admision_date = worker.work_start_date
                this.form_disabled.admision_date = worker.work_start_date ? true : false

                this.autocompleteDataSalary(worker.salary)

                this.autocompleteDataPayment(worker)

                this.changePayrollPeriod()

            },
            async autocompleteDataSalary(worker_salary){

                this.form.accrued.worked_days = this.quantity_days_month
                this.form.accrued.salary = parseFloat(worker_salary)
                this.form.accrued.total_base_salary = parseFloat(worker_salary) //salario completo
                this.calculateTransportationAllowance()

                //recalcular pagos(totales) de las horas extras, estas se calculan en base al salario base
                await this.$refs.componentDocumentPayrollExtraHours.recalculateDataExtraHours()

            },
            changePayrollPeriod(){
                
                let payroll_period = _.find(this.payroll_periods, { id : this.form.payroll_period_id })
                this.cleanPaymentDates()

                // mensual
                if(payroll_period.id == 5){
                    // agregar pago con el ultimo dia del mes
                    this.clickAddPaymentDate(moment().endOf('month').format('YYYY-MM-DD'))
                }

                // quincenal
                if(payroll_period.id == 4){
                    
                    const start_date = moment().startOf('month')
                    const end_date = moment().endOf('month').format('YYYY-MM-DD')
                    let add_quantity_days = parseInt(moment().daysInMonth() / 2) - 1
                    const fortnight = start_date.add(add_quantity_days, 'days').format('YYYY-MM-DD')

                    // quincena
                    this.clickAddPaymentDate(fortnight)

                    // fin de mes
                    this.clickAddPaymentDate(end_date)
                    
                }

            },
            cleanPaymentDates(){
                this.form.payment_dates = []
            },
            getPaymentPerDay(){
                return this.form.accrued.total_base_salary / this.quantity_days_month
            },
            getTransportationAllowancePerDay(){
                return this.advanced_configuration.transportation_allowance / this.quantity_days_month
            },
            changeWorkedDays(){

                this.form.accrued.salary = _.round(this.getPaymentPerDay() * this.form.accrued.worked_days, 2)

                this.calculateTransportationAllowance()
                this.calculateTotal()

            },
            calculateTransportationAllowance(){

                // validar si corresponde subsidio y asignar valor
                let minimum_salary = parseFloat(this.advanced_configuration.minimum_salary)

                if(this.form.accrued.total_base_salary <= (minimum_salary * 2)){

                    this.form.accrued.transportation_allowance = _.round(this.getTransportationAllowancePerDay() * this.form.accrued.worked_days, 2)

                }else{
                    this.form.accrued.transportation_allowance = undefined
                }

            },
            // async changeAccruedSalary(){

            //     await this.autocompleteDataSalary(this.form.accrued.salary)

            //     //recalcular pagos(totales) de las horas extras, estas se calculan en base al salario base
            //     await this.$refs.componentDocumentPayrollExtraHours.recalculateDataExtraHours()

            //     //calcular total devengados
            //     await this.calculateTotalAccrued()

            // },
            autocompleteDataPayment(worker){

                if(worker.payment){

                    this.form.payment = worker.payment
                    this.form_disabled.payment = true

                }else{

                    this.form.payment = {
                        payment_method_id: null,
                        bank_name: null,
                        account_type: null,
                        account_number: null,
                    }

                    this.form_disabled.payment = false
                }
                
                this.changePaymentMethod()

            },
            async submit() {

                if(!this.form.resolution_number){
                    return this.$message.error("El número de resolución es obligatorio, debe asignarle un valor")
                }
 
                this.loading_submit = true
                
                await this.$http.post(`/${this.resource}`, this.form).then(response => {
                    // console.log(response)
                    if (response.data.success) {
                        this.resetForm()
                        this.recordId = response.data.data.id
                        this.showDialogDocumentPayrollOptions = true
                    }
                    else {
                        this.$message.error(response.data.message)
                    }
                }).catch(error => {

                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    }
                    else {
                        this.$message.error(error.response.data.message)
                    }


                }).then(() => {
                    this.loading_submit = false;
                });
            }, 
        }
    }
</script>
