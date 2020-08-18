<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Reporte impuestos</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th  class="text-left">Fecha emisión</th>
                            <th  class="text-center">Cliente</th>
                            <th  class="">Documento</th>
                            <th  class="text-center">Base</th>
                            <th  class="text-center">Descuento</th>
                        </tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>
                            <td  class="text-left">{{row.created_at}}</td>
                            <td  class="text-center">{{row.customer.name}}</td>
                            <td class="">
                                <div>{{row.type_document.name}}</div>
                                <div>{{row.prefix}}{{row.number}}<template v-if="row.type_document_id != 1">({{row.reference.prefix}}{{row.reference.number}})</template></div>
                            </td>
                            <td class="text-center">$ {{row.total}}</td>
                            <td class="text-center">$ {{row.total_discount}}</td>
                        </tr>
                        <!--<tr slot="footing">
                            <td class="text-center" :colspan="(columns.length - 2)"><strong>Totales:</strong></td>
                            <td><strong>{{ratePrefix()}}{{getSaleTotal() | numberFormat}}</strong></td>
                            <td><strong>{{ratePrefix()}}{{getTotalDiscount() | numberFormat}}</strong></td>
                            <td v-for="tax in taxTitles"><strong>{{ratePrefix()}}{{getTaxTotal(tax) | numberFormat}}</strong></td>
                        </tr>-->
                    </data-table>
                </div>
        </div>

    </div>
</template>

<script>

    import DataTable from '../../components/DataTableTax.vue'

    export default {
        components: {DataTable},
        data() {
            return {
                resource: 'reports/taxes',
                form: {},

            }
        },
        async created() {
        },
        methods: {


        }
    }
</script>
