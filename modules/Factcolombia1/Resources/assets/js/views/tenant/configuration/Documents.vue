<template>
    <main class="content">
        <div class="container-fluid">
            <v-app id="configuration">
                <div class="content-header">
                    <h1>Datos de la compañia</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">{{company.name}}</li>
                        </ol>
                    </nav>
                </div>
                <v-container>
                    <v-layout row wrap>
                        <v-flex lg12>
                            <v-card>
                                <v-data-table :headers="filterHeaders" :items="typeDocuments" :loading="loadDataTable" class="elevation-1">
                                    <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
                                    <template slot="items" slot-scope="props">
                                        <td v-if="showColumn(columns, 'id')">{{props.item.id}}</td>
                                        <td v-if="showColumn(columns, 'name')">{{props.item.name}}</td>
                                        <td v-if="showColumn(columns, 'prefix')">{{props.item.prefix}}</td>
                                        <td v-if="showColumn(columns, 'from')">{{props.item.from}}</td>
                                        <td v-if="showColumn(columns, 'to')">{{props.item.to}}</td>
                                        <td v-if="showColumn(columns, 'resolution_number')">{{props.item.resolution_number}}</td>
                                        <td v-if="showColumn(columns, 'resolution_date')">{{props.item.resolution_date}}</td>
                                        <td v-if="showColumn(columns, 'resolution_date_end')">{{props.item.resolution_date_end}}</td>
                                        <td v-if="showColumn(columns, 'technical_key')">{{props.item.technical_key}}</td>
                                        <td v-if="showColumn(columns, 'actions')">
                                            <v-icon small color="info" class="mr-2" @click="editItem(props.item)">edit</v-icon>
                                        </td>
                                    </template>
                                </v-data-table>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-app>
            <v-app id="item_form" style="height: 0px;">
                <v-layout row justify-center>
                    <v-dialog v-model="dialog" persistent max-width="700" transition="dialog-bottom-transition">
                        <v-card>
                            <v-card-title>
                                <!-- <span class="headline">{{title}}</span> -->
                            </v-card-title>
                            <v-list three-line subheader>
                                <v-form data-vv-scope="item">
                                    <v-container>
                                        <v-layout row wrap>
                                                <v-flex v-if="item.code == 1" xs12 sm12 md12 lg12>
                                                    <v-text-field v-model="item.resolution_number" v-validate="'required|numeric|max:20'" :error-messages="errors.collect(`type_documents_${item.id}.resolution_number`)" data-vv-name="resolution_number" :counter="20" label="Número de resolución *" required></v-text-field>
                                                </v-flex>
                                                <v-flex v-if="item.code == 1" xs12 sm12 md6 lg6 xl4>
                                                    <v-menu ref="resolution_date" v-model="item.menu" :close-on-content-click="false" :nudge-right="40" :return-value.sync="item.resolution_date" lazy transition="scale-transition" offset-y full-width min-width="290px">
                                                        <template v-slot:activator="{on}">
                                                            <v-text-field v-model="item.resolution_date" label="Fecha resolución *" prepend-icon="event" readonly v-on="on"></v-text-field>
                                                        </template>
                                                        <v-date-picker v-model="item.resolution_date" locale="es-co" :max="toDate" no-title scrollable>
                                                            <v-spacer></v-spacer>
                                                            <v-btn flat color="primary" @click="item.menu = false">Cancelar</v-btn>
                                                            <v-btn flat color="primary" @click="$refs.resolution_date.save(item.resolution_date)">OK</v-btn>
                                                        </v-date-picker>
                                                    </v-menu>
                                                </v-flex>
                                                <v-flex v-if="item.code == 1" xs12 sm12 md6 lg6 xl4>
                                                    <v-menu ref="resolution_date_end" v-model="item.menu_end" :close-on-content-click="false" :nudge-right="40" :return-value.sync="item.resolution_date_end" lazy transition="scale-transition" offset-y full-width min-width="290px">
                                                        <template v-slot:activator="{on}">
                                                            <v-text-field v-model="item.resolution_date_end" label="Fecha resolución hasta *" prepend-icon="event" readonly v-on="on"></v-text-field>
                                                        </template>
                                                        <v-date-picker v-model="item.resolution_date_end" locale="es-co" :min="toDate" no-title scrollable>
                                                            <v-spacer></v-spacer>
                                                            <v-btn flat color="primary" @click="item.menu_end = false">Cancelar</v-btn>
                                                            <v-btn flat color="primary" @click="$refs.resolution_date_end.save(item.resolution_date_end)">OK</v-btn>
                                                        </v-date-picker>
                                                    </v-menu>
                                                </v-flex>
                                                <v-flex v-if="item.code == 1" xs12 sm12 md6 lg6>
                                                    <v-text-field v-model="item.technical_key" v-validate="'required|alpha_dash|max:80'" :error-messages="errors.collect(`type_documents_${item.id}.technical_key`)" data-vv-name="technical_key" :counter="80" label="Clave técnica *" required></v-text-field>
                                                </v-flex>
                                            <v-flex xs12 sm12 md6 lg4>
                                                <v-text-field v-model="item.name" class="input-uppercase" v-validate="'required|max:50'" :error-messages="errors.collect('item.name')" data-vv-name="name" :counter="50" label="Nombre *" required></v-text-field>
                                            </v-flex>
                                            <v-flex xs12 sm12 md6 lg4>
                                                <v-text-field v-model="item.prefix" class="input-uppercase" v-validate="'required|alpha_dash|max:11'" :error-messages="errors.collect('item.prefix')" data-vv-name="code" :counter="11" label="Prefijo *" required></v-text-field>
                                            </v-flex>
                                            <v-flex xs12 sm12 md6 lg4>
                                                <v-text-field v-model="item.from" class="input-uppercase" v-validate="'required|alpha_dash|max:11'" :error-messages="errors.collect('item.from')" data-vv-name="code" :counter="11" label="Desde *" required></v-text-field>
                                            </v-flex>
                                            <v-flex xs12 sm12 md6 lg4>
                                                <v-text-field v-model="item.to" class="input-uppercase" v-validate="'required|alpha_dash|max:11'" :error-messages="errors.collect('item.to')" data-vv-name="code" :counter="11" label="Hasta *" required></v-text-field>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="warning" flat @click="dialog = false">Cerrar</v-btn>
                                        <v-btn color="bee" flat :loading="loadingCompany" @click="validate(`type_documents_${item.id}`, 'type_document', typeDocuments, item)" class="text-white">Guardar</v-btn>
                                    </v-card-actions>
                                </v-form>
                            </v-list>
                        </v-card>
                    </v-dialog>
                </v-layout>
            </v-app>
        </div>
    </main>
</template>

<script>
    import Helper from '../../../mixins/Helper';

    export default {
        mixins: [Helper],
        props: {
            route: {
                required: true
            }
        },
        data: () => ({
            typeIdentityDocuments: [],
            loadingCompany: false,
            loadingOther: false,
            typeObligations: [],
            type_documents: {},
            typeDocuments: [],
            versionUbls: [],
            typeRegimes: [],
            departments: [],
            currencies: [],
            countries: [],
            ambients: [],
            cities: [],

            dialog: false,
            item: {},
            loadDataTable: false,
            items: [],
            columns: [{
                text: '#',
                value: 'id',
                canHide: false,
                show: true
            }, {
                text: 'Nombre',
                value: 'name',
                canHide: false,
                show: true
            }, {
                text: 'Prefijo',
                value: 'prefix',
                canHide: false,
                show: true
            }, {
                text: 'Desde',
                value: 'from',
                canHide: false,
                show: true
            }, {
                text: 'Hasta',
                value: 'to',
                canHide: false,
                show: true
            }, {
                text: 'Número de resolución',
                value: 'resolution_number',
                canHide: false,
                show: true
            }, {
                text: 'Fecha resolución',
                value: 'resolution_date',
                canHide: false,
                show: true
            }, {
                text: 'Fecha resolución hasta',
                value: 'resolution_date_end',
                canHide: false,
                show: true
            }, {
                text: 'Clave técnica',
                value: 'technical_key',
                canHide: false,
                show: true
            }, {
                text: 'Acciones',
                value: 'actions',
                sortable: false,
                canHide: true,
                show: true
            }]
        }),
        computed: {
            filterHeaders() {
                return this.columns.filter(column => column.show);
            }
        },
        mounted() {
            this.refresh();
        },
        methods: {
            refresh() {
                axios.post(`/client/configurationAll`).then(response => {
                    this.$setLaravelMessage(response.data);

                    this.typeIdentityDocuments = response.data.typeIdentityDocuments;
                    this.typeDocuments = response.data.typeDocuments;
                    this.typeObligations = response.data.typeObligations;
                    this.typeRegimes = response.data.typeRegimes;
                    this.versionUbls = response.data.versionUbls;
                    this.currencies = response.data.currencies;
                    this.countries = response.data.countries;
                    this.ambients = response.data.ambients;

                    if (this.company.country_id != null) this.departmentss(true);
                    if (this.company.department_id != null) this.citiess(true);
                }).catch(error => {
                    this.$setLaravelValidationErrorsFromResponse(error.response.data);
                    this.$setLaravelErrors(error.response.data);
                }).then(() => {});
            },
            departmentss(mounted = false) {
                if (!mounted) {
                    this.company.department_id = null;
                    this.company.city_id = null;
                    this.departments = [];
                }

                if (this.company.country_id != null) this.getDepartment(this.company.country_id).then(rows => this.departments = rows);
            },
            citiess(mounted = false) {
                if (!mounted) this.company.city_id = null;

                this.cities = [];

                if (this.company.department_id != null) this.getCities(this.company.department_id).then(rows => this.cities = rows);
            },
            url(scope = 'company', model = null, models = null, modelObject = null) {
                let Data = new FormData(this.company);

                for (var row in this.company) Data.append(row, this.company[row]);
                Data.append('_method', 'PUT');

                return {
                    url: (scope == 'company') ? `/client/configuration/${scope}/${this.company.id}` : `/client/configuration/${model}/${modelObject.id}`,
                    data: (scope == 'company') ? Data : models.find(model => model.id == modelObject.id)
                }
            },
            editItem(item) {
                this.item = JSON.parse(JSON.stringify(item));
                this.dialog = true;
            },
            validate(scope, model = null, models = null, modelObject = null) {
                debugger
                this.$validator.validateAll(scope).then(valid => {
                    if (valid) {
                       // let url = this.url(scope, model, models, modelObject);

                        modelObject.prefix = modelObject.prefix.toUpperCase()
                        
                        this.loadingCompany = true;
                        this.loadDataTable = true;
                       // return false
                        axios.post(`/client/configuration/type_document/${modelObject.id}`, modelObject).then(response => {
                            if (response.data.success) this.refresh();
                            
                            this.$setLaravelMessage(response.data);
                        }).catch(error => {
                            this.$setLaravelValidationErrorsFromResponse(error.response.data);
                            this.$setLaravelErrors(error.response.data);
                        }).then(() => {
                            this.loadingCompany = false;
                            this.dialog = false;
                            this.loadDataTable = false;
                        });
                    }
                });
            }
        }
    }
</script>
<style lang="scss">
    .input-uppercase input {
        text-transform: uppercase
    }
</style>
