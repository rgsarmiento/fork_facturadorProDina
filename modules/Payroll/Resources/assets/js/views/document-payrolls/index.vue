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
                        <th>Nómina</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.date_of_issue }}</td>
                        <td>{{ row.worker_full_name }}</td>  
                        <td>{{ row.number_full }}</td>  
                        <td class="text-right">
                            <!-- <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Editar</button> -->
                            <!-- <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)">Eliminar</button> -->
                        </td>
                    </tr>
                </data-table>
            </div>

            <!-- <workers-form :showDialog.sync="showDialog"
                        :recordId="recordId"></workers-form> -->

        </div>
    </div>
</template>
<script>

    // import WorkersForm from './form.vue'
    import DataTable from '@components/DataTableResource.vue'
    import {deletable} from '@mixins/deletable'

    export default {
        mixins: [deletable],
        components: { DataTable},
        data() {
            return {
                showDialog: false,
                resource: 'payroll/document-payrolls',
                recordId: null,
            }
        },
        created() { 
        },
        methods: { 
            // clickCreate(recordId = null) {
            //     this.recordId = recordId
            //     this.showDialog = true
            // },  
        }
    }
</script>
