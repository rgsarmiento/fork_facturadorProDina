<template>
    <el-dialog :title="titleDialog" width="70%" :visible="showDialog" @open="create" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" append-to-body>

        <el-tabs v-model="activeName">
            <el-tab-pane label="Horas extras diurnas (25%)" name="heds">
                    
                <div class="row mt-2">
                    <div class="col-md-12" v-if="errors['accrued.heds']">
                        <div class="form-group" :class="{'has-danger': errors['accrued.heds']}">
                            <small class="form-control-feedback" v-text="errors['accrued.heds'][0]"></small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table>
                            <thead>
                                <tr width="100%">
                                    <template v-if="form.accrued.heds.length>0">
                                        <th class="pb-2">Fecha</th>
                                        <th class="pb-2">Hora inicio - Hora término</th>
                                        <th class="pb-2">Cantidad</th>
                                        <!-- <th class="pb-2">Porcentaje</th> -->
                                        <th class="pb-2">Pago</th>
                                    </template>
                                    <th width="10%"><a href="#" @click.prevent="clickAddExtraHour(form.accrued.heds, 'heds')" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in form.accrued.heds" :key="index"> 
                                    <td>
                                        <div class="form-group mb-2 mr-2"  >
                                            <el-date-picker 
                                                v-model="row.start_end_date" 
                                                type="date"
                                                value-format="yyyy-MM-dd" 
                                                :clearable="false"
                                                @change="changeStartEndDate(form.accrued.heds, index)"
                                            ></el-date-picker>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group mb-2 mr-2"  >
                                            
                                            <el-time-picker
                                                is-range
                                                v-model="row.start_end_time"
                                                range-separator="H"
                                                format="HH:mm"
                                                value-format="HH:mm"
                                                :clearable="false"
                                                @change="changeStartEndTime(form.accrued.heds, index, 'heds')"
                                                >
                                            </el-time-picker>
                                        </div>
                                    </td> 
                                    <td>
                                        <div class="form-group mb-2 mr-2"  >
                                            <el-input-number v-model="row.quantity" :min="0" controls-position="right"></el-input-number>
                                        </div>
                                    </td>
                                    <!-- <td>
                                        <div class="form-group mb-2 mr-2"  >
                                            <el-input-number v-model="row.percentage" :min="0" controls-position="right"></el-input-number>
                                        </div>
                                    </td> -->
                                    <td>
                                        <div class="form-group mb-2 mr-2"  >
                                            <el-input-number v-model="row.payment" :min="0" controls-position="right"></el-input-number>
                                        </div>
                                    </td>

                                    <td class="series-table-actions text-center">
                                        <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelExtraHour(form.accrued.heds, index)">
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
        </el-tabs>

        <span slot="footer" class="dialog-footer">
            <el-button @click="clickClose">Cerrar</el-button>
        </span>
    </el-dialog>
</template>

<script>
import moment from 'moment'
    export default {
        props: ['showDialog', 'form', 'errors'],
        data() {
            return {
                titleDialog: 'Horas extras',
                loading: false,
                activeName: 'heds',
                resource: 'payroll/document-payrolls',
                extra_hour_types: [],
                type_overtime_surcharges: [],
            }
        },
        async created() {
            await this.initData()
            await this.getTable()
        },
        methods: {
            getTable(){
                this.$http.get(`/${this.resource}/table/type_overtime_surcharges`).then((response) => {
                    this.type_overtime_surcharges = response.data
                })
            },
            initData(){

            },
            changeStartEndTime(array_hours, index, type){
                // console.log(array_hours[index].start_end_time)

                // calcular cantidad horas y valores adicionales
                this.calculateExtraHours(array_hours, index, type)

            },
            calculateExtraHours(array_hours, index, type){

                const start_end_time = array_hours[index].start_end_time
                let start_time = start_end_time[0]
                let end_time = start_end_time[1]
                
                let quantity = _.round(moment(end_time, "HH:mm:ss").diff(moment(start_time, "HH:mm:ss"), 'hours', true), 2)

                let type_overtime_surcharge = this.getTypeOvertimeSurcharge(type)
                let percentage_id = type_overtime_surcharge.id
                let price_per_hour_extra = this.getPricePerExtraHour(type_overtime_surcharge.percentage)

                //calcular valores finales
                array_hours[index].quantity = quantity
                array_hours[index].percentage = percentage_id
                array_hours[index].payment =  _.round(price_per_hour_extra * quantity, 2)

                this.setStartEndTimeFormatApi(array_hours, index)
            
            },
            changeStartEndDate(array_hours, index){
                this.setStartEndTimeFormatApi(array_hours, index)
            },
            setStartEndTimeFormatApi(array_hours, index){

                const start_end_time = array_hours[index].start_end_time
                let start_time = array_hours[index].start_end_date + " " + start_end_time[0]+":00"
                let end_time = array_hours[index].start_end_date + " " + start_end_time[1]+":00"

                // asignar campos hora inicio y termino para enviar a api de acuerdo al formato
                array_hours[index].start_time = moment(start_time).format("YYYY-MM-DD[T]HH:mm:ss")
                array_hours[index].end_time = moment(end_time).format("YYYY-MM-DD[T]HH:mm:ss")

            },
            getIdTypeOvertimeSurchargeFromType(type){

                let id = null

                switch (type) {
                    case 'heds':
                        id = 1
                        break;
                
                    default:
                        break;
                }

                return id

            },
            getPricePerExtraHour(percentage){
                // obtener el precio por hora, incluido el % agregado al ser hora extra
                return _.round((parseFloat(this.form.accrued.salary) / 240) * (1 + percentage / 100), 2)
            },
            getTypeOvertimeSurcharge(type)
            {
                return _.find(this.type_overtime_surcharges, {id : this.getIdTypeOvertimeSurchargeFromType(type)})
            },
            clickAddExtraHour(array_hours, type){

                let type_overtime_surcharge = this.getTypeOvertimeSurcharge(type)

                let percentage_id = type_overtime_surcharge.id
                let quantity = 1
                let price_per_hour = (parseFloat(this.form.accrued.salary) / 240) * (1 + type_overtime_surcharge.percentage / 100)
                let payment = _.round(price_per_hour * quantity, 2)

                array_hours.push({
                    start_end_date: moment().format("YYYY-MM-DD"),
                    start_end_time: [
                        moment().format("HH:mm"),
                        moment().add(1, 'hours').format("HH:mm")
                    ],
                    quantity :  quantity,
                    percentage :  percentage_id,
                    payment :  payment,
                    start_time: null,
                    end_time: null,
                })

            },
            clickCancelExtraHour(array_hours, index){
                array_hours.splice(index, 1)
            },
            create() { 

            }, 
            clickClose() {
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>
