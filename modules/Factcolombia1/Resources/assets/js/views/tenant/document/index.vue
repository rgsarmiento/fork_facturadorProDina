<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Listado de documentos</span> </li>
                <!-- <li><span class="text-muted">Facturas - Notas <small>(crédito y débito)</small> - Boletas - Anulaciones</span></li> -->
            </ol> 
            <div class="right-wrapper pull-right" >
                
                <a :href="`/${resource}/create`" class="btn btn-custom btn-sm  mt-2 mr-2"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <div class="card mb-0">
            <div class="data-table-visible-columns">
                <!-- <el-button class="submit" type="success" @click.prevent="clickDownloadReportPagos('excel')"><i class="fa fa-file-excel" ></i>  Descargar Pagos</el-button> -->
                <!-- <el-dropdown :hide-on-click="false">
                    <el-button type="primary">
                        Mostrar/Ocultar columnas<i class="el-icon-arrow-down el-icon--right"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="(column, index) in columns" :key="index">
                            <el-checkbox v-model="column.visible">{{ column.title }}</el-checkbox>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown> -->
            </div>
            <div class="card-body ">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th class="text-center">Fecha Emisión</th>
                        <th>Cliente</th>
                        <th>Documento</th>
                        <th>Estado DIAN</th>
                        <th>Acuse recibido cliente</th>
                        <th class="text-center">Moneda</th>
                        <th class="text-right">T.Venta</th>
                        <th class="text-right">T.Descuentos</th>
                        <th class="text-right">T.Impuestos</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Descargas</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }" >
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>
                        <td>{{ row.number_full }}<br/>
                            <small v-text="row.type_document_name"></small><br/>
                        </td>
                        <td class="text-center">{{ row.state_document_name }}</td>
                        <td class="text-center">{{ row.acknowledgment_received }}</td>
                        <td class="text-center">{{ row.currency_name }}</td>

                        <td class="text-right">{{ row.sale }}</td>
                        <td class="text-right">{{ row.total_discount }}</td>
                        <td class="text-right">{{ row.total_tax }}</td>
                        <td class="text-right">{{ row.subtotal }}</td>
                        <td class="text-right">{{ row.total }}</td>
                       
                        <td class="text-right" > 
                            <button type="button" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                                    @click.prevent="clickDownload(row.download_xml)"
                                   >XML</button>
                            <button type="button" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                                    @click.prevent="clickDownload(row.download_pdf)"
                                   >PDF</button>
 
                        </td>
                        <td class="text-right" > 
                                   
                            <a :href="`/${resource}/note/${row.id}`" class="btn waves-effect waves-light btn-xs btn-warning m-1__2"
                               >Nota</a>

                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                                    @click.prevent="clickOptions(row.id)">Opciones</button>
 
                        </td>
                    </tr>
                </data-table>
            </div>
 
            <document-options :showDialog.sync="showDialogOptions"
                              :showDownload="false"
                              :recordId="recordId"
                              :showClose="true"></document-options>
        </div>
    </div>
</template>

<script>
 
    import DataTable from '@components/DataTable.vue'
    import DocumentOptions from './partials/options.vue'

    export default {
        components: {DataTable, DocumentOptions},
        data() {
            return {
                showDialogReportPayment:false,
                showDialogVoided: false,
                showImportDialog: false,
                showDialogCDetraction: false,
                showImportSecondDialog: false,
                resource: 'co-documents',
                recordId: null,
                showDialogOptions: false,
                showDialogPayments: false, 
            }
        },
        created() {
        },
        methods: {
            clickVoided(recordId = null) {
                this.recordId = recordId
                this.showDialogVoided = true
            },
            clickDownload(download) {
                window.open(download, '_blank');
            }, 
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            }, 
        }
    }
</script>
