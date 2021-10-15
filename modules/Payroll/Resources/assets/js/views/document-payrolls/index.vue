<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Nóminas</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a :href="`/${resource}/create`" class="btn btn-custom btn-sm  mt-2 mr-2"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de Nóminas</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading" width="100%">
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Empleado</th>
                        <th class="text-center">Nómina</th>
                        <th class="text-center">Salario</th>
                        <th class="text-center">T. Devengados</th>
                        <th class="text-center">T. Deducciones</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.date_of_issue }}</td>
                        <td>{{ row.worker_full_name }}</td>  
                        <td class="text-center">{{ row.number_full }}</td>  
                        <td class="text-center">{{ row.salary }}</td>  
                        <td class="text-center">{{ row.accrued_total }}</td>  
                        <td class="text-center">{{ row.deductions_total }}</td>  
                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickOptions(row.id)">Opciones</button>
                            <!-- <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)">Eliminar</button> -->
                        </td>
                    </tr>
                </data-table>
            </div>
 

            <document-payroll-options :showDialog.sync="showDialogDocumentPayrollOptions"     
                            :recordId="recordId"
                            :showDownload="true"
                            :showClose="true">
                            </document-payroll-options>
        </div>
    </div>
</template>
<script>

    // import WorkersForm from './form.vue'
    import DocumentPayrollOptions from './partials/options.vue' 
    import DataTable from '@components/DataTableResource.vue'
    import {deletable} from '@mixins/deletable'

    export default {
        mixins: [deletable],
        components: { DataTable, DocumentPayrollOptions},
        data() {
            return {
                showDialog: false,
                resource: 'payroll/document-payrolls',
                recordId: null,
                showDialogDocumentPayrollOptions:false,
            }
        },
        created() { 
        },
        methods: { 
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogDocumentPayrollOptions = true
            },  
        }
    }
</script>
