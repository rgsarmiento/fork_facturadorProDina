<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Configuracion de la Empresa</h3>
        </div>
        <div class="tab-content">
            <div class="general-data">
                <form autocomplete="off">
                    <div class="form-body">
                        <h3 class="my-0">Datos Generales</h3>
                        <div class="row mt-4">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="control-label">Numero de Identificacion</label>
                                    <el-input
                                        v-model="identification_number"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">DV</label>
                                    <el-input
                                        v-model="dv"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="control-label">Registro Mercantil</label>
                                    <el-input
                                        v-model="merchant_registration"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Direccion</label>
                                    <el-input
                                        v-model="address"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Telefono</label>
                                    <el-input
                                        v-model="phone"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Lenguaje</label>
                                    <el-input
                                        v-model="languaje"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Impuesto</label>
                                    <el-input
                                        v-model="tax_profile"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Pais</label>
                                    <el-input
                                        v-model="country"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Tipo de Documento</label>
                                    <el-input
                                        v-model="type_document"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Tipo de Operacion</label>
                                    <el-input
                                        v-model="type_operation"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Tipo Entorno</label>
                                    <el-input
                                        v-model="type_environmet"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Tipo Moneda</label>
                                    <el-input
                                        v-model="type_currency"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Tipo de Organizacion</label>
                                    <el-input
                                        v-model="type_organization"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Municipio</label>
                                    <el-input
                                        v-model="municipality"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Tipo Responsabilidad</label>
                                    <el-input
                                        v-model="type_liability"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Tipo de Regimen</label>
                                    <el-input
                                        v-model="type_regime"
                                        :disabled="true">
                                    </el-input>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-content">
            <div class="software">
                <form autocomplete="off">
                    <div class="form-body">
                        <h3 class="my-0">Software</h3>
                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Id Software *</label>
                                    <el-input
                                        v-model="id_software"
                                        placeholder="Introduzca el Id Software asignado por la DIAN."
                                        :disabled="false">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Pin Software *</label>
                                    <el-input
                                        v-model="pin_software"
                                        placeholder="Digite el pin del Software"
                                        :disabled="false">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Test Set ID *</label>
                                    <el-input
                                        v-model="test_set_id"
                                        placeholder="Introduzca el codigo del Set de Pruebas para habilitacion."
                                        :disabled="false">
                                    </el-input>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions text-right mt-4">
                            <el-button class="submit" type="primary" native-type="submit">Guardar</el-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-content">
            <div class="certificado">
                <form autocomplete="off">
                    <div class="form-body">
                        <h3 class="my-0">Certificado</h3>
                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <el-upload
                                        ref="fileCertificado"
                                        :auto-upload="false"
                                        :on-change="handleChangeFileCertificado"
                                        :limit="1"
                                        drag
                                        action="''"
                                    >
                                        <i class="el-icon-upload"></i>
                                        <div class="el-upload__text">
                                            Suelta tu archivo aquí o
                                            <em>haz clic para cargar</em>
                                        </div>
                                        <div
                                            slot="tip"
                                            class="el-upload__tip"
                                        >Solo archivos jpg/png con un tamaño menor de 500kb</div>
                                    </el-upload>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Password Certificado *</label>
                                    <el-input
                                        v-model="id_software"
                                        placeholder="Introduzca el password del certificado digital."
                                        :disabled="false">
                                    </el-input>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions text-right mt-4">
                            <el-button class="submit" type="primary" native-type="submit">Guardar</el-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-content">
            <div class="resolution">
                <form autocomplete="off">
                    <div class="form-body">
                        <h3 class="my-0">Resolucion de Facturacion</h3>
                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Tipo de Documento *</label>
                                    <el-select v-model="type_document_id" filterable remote class="border-left rounded-left border-info" popper-class="el-select-type-document"
                                        dusk="type_document_id"
                                        placeholder="Seleccione el tipo de documento."
                                        :remote-method="searchRemoteCustomers"
                                        @keyup.enter.native="keyupCustomer"
                                        :loading="loading_search"
                                        @change="changeCustomer">

                                        <el-option v-for="option in typeDocuments" :key="option.id" :value="option.id" :label="option.name"></el-option>

                                    </el-select>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Prefijo *</label>
                                    <el-input
                                        v-model="prefix"
                                        placeholder="Digite el prefijo de la resolucion"
                                        :disabled="false">
                                    </el-input>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Nro Resolucion *</label>
                                    <el-input
                                        v-model="test_set_id"
                                        placeholder="Digite el numero de resolucion."
                                        :disabled="false">
                                    </el-input>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Fecha Resolucion</label>
                                    <el-date-picker v-model="date_resolution" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue" :picker-options="datEmision"></el-date-picker>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Fecha Desde</label>
                                    <el-date-picker v-model="date_from" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue" :picker-options="datDesde"></el-date-picker>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">Fecha Hasta</label>
                                    <el-date-picker v-model="date_to" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue" :picker-options="datHasta"></el-date-picker>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions text-right mt-4">
                            <el-button class="submit" type="primary" native-type="submit">Guardar</el-button>
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
            identification_number: '900166483',
            dv: '1',
            merchant_registration: '00000-00',
            address: 'CRA 25 NRO 41-65 LC 2',
            phone: '7423171',
            languaje: 'Spanish; Castillian',
            tax_profile: 'IVA',
            country: 'Colombia',
            type_document: 'NIT',
            type_operation: 'Estandar',
            type_environmet: 'Pruebas',
            type_currency: 'Peso colombiano',
            type_organization: 'Persona Juridica',
            municipality: 'Calarca',
            type_liability: 'Obligado a Facturar Electrónicamente Modelo 2242',
            type_regime: 'Régimen Simple',
            id_software: '',
            pin_software: '',
            test_set_id: '',
            typeDocuments: [
                { id: 1, name: "Factura de Venta Nacional" },
                { id: 2, name: "Factura de Exportación" },
                { id: 3, name: "Factura de Contingencia" },
                { id: 4, name: "Nota Crédito" },
                { id: 5, name: "Nota Débito" },
                { id: 6, name: "ZIP" }
            ],
            type_document_id: '',
            errors: {

            },
            fileCertificado: "",
            typeIdentityDocuments: [],
            loadingCompany: false,
            loadingOther: false,
            loading_form: true,
            typeObligations: [],
            type_documents: {},
            versionUbls: [],
            typeRegimes: [],
            departments: [],
            currencies: [],
            countries: [],
            ambients: [],
            cities: []
        }),

        mounted() {
            if (window.File && window.FileReader && window.FileList && window.Blob)
                console.log("ok.");
            else
                alert("The File APIs are not fully supported in this browser.");
        },

        methods: {
            pickFile() {
                this.$refs.certificate.click();
            },

            onFilePicked(e) {
                let files = e.target.files;

                if (files[0] !== undefined) {
                    this.$set(this.company, "certificate_name", files[0].name);
                    return;
                }
                this.$set(this.company, "certificate_name", "");
            },

            departmentss(mounted = false) {
                if (!mounted) {
                    this.company.department_id = null;
                    this.company.city_id = null;
                    this.departments = [];
                }
                if (this.company.country_id != null)
                    this.getDepartment(this.company.country_id).then(
                        rows => (this.departments = rows)
                    );
            },

            citiess(mounted = false) {
                if (!mounted) this.company.city_id = null;
                    this.cities = [];
                if (this.company.department_id != null)
                    this.getCities(this.company.department_id).then(
                        rows => (this.cities = rows)
                    );
            },

            url(scope = "company", model = null, models = null, modelObject = null) {
                let Data = new FormData(this.company);

                if (this.$refs.certificate.files.length > 0)
                    Data.append(
                        "certificate",
                        this.$refs.certificate.files[0],
                        this.$refs.certificate.files[0].name
                    );
                for (var row in this.company) Data.append(row, this.company[row]);
                    Data.append("_method", "PUT");
                return {
                    url:
                        scope == "company"
                        ? `${this.route}/${scope}/${this.company.id}`
                        : `${this.route}/${model}/${modelObject.id}`,
                    data:
                        scope == "company"
                        ? Data
                        : models.find(model => model.id == modelObject.id)
                };
            },

            validate(scope, model = null, models = null, modelObject = null) {
                this.company.certificate64 = document.getElementById("base64").value;
                this.$validator.validateAll(scope).then(valid => {
                    if (valid) {
                        this.loadingCompany = true;
                        axios
                            .post(`${this.route}/storeServiceCompanie`, this.company)
                            .then(response => {
                                this.$setLaravelMessage(response.data);
                            })
                            .catch(error => {
                                this.$setLaravelValidationErrorsFromResponse(error.response.data);
                                this.$setLaravelErrors(error.response.data);
                            })
                           .then(() => {
                                this.loadingCompany = false;
                            });
                    }
                });
            },

            validateSoftware(scope, model = null, models = null, modelObject = null) {
                this.$validator.validateAll(scope).then(valid => {
                    if (valid) {
                        this.loadingCompany = true;
                        axios
                            .post(`${this.route}/storeServiceCompanieSoftware`, this.software)
                            .then(response => {
                                this.$setLaravelMessage(response.data);
                            })
                            .catch(error => {
                                this.$setLaravelValidationErrorsFromResponse(error.response.data);
                                this.$setLaravelErrors(error.response.data);
                            })
                            .then(() => {
                                this.loadingCompany = false;
                            });
                    }
                });
            },

            validateCertificate(scope, model = null, models = null, modelObject = null) {
                this.$validator.validateAll(scope).then(valid => {
                    if (valid) {
                        this.certificate.certificate64 = document.getElementById("base64").value;
                        this.loadingCompany = true;
                        axios
                            .post(
                                `${this.route}/storeServiceCompanieCertificate`,
                                this.certificate
                            )
                            .then(response => {
                                this.$setLaravelMessage(response.data);
                            })
                            .catch(error => {
                                this.$setLaravelValidationErrorsFromResponse(error.response.data);
                                this.$setLaravelErrors(error.response.data);
                            })
                            .then(() => {
                                this.loadingCompany = false;
                            });
                    }
                });
            },

            validateResolution(scope, model = null, models = null, modelObject = null) {
                this.$validator.validateAll(scope).then(valid => {
                    if (valid) {
                        this.loadingCompany = true;
                        axios
                            .post(
                            `${this.route}/storeServiceCompanieResolution`,
                            this.resolution
                            )
                            .then(response => {
                                this.$setLaravelMessage(response.data);
                            })
                            .catch(error => {
                                this.$setLaravelValidationErrorsFromResponse(error.response.data);
                                this.$setLaravelErrors(error.response.data);
                            })
                            .then(() => {
                                this.loadingCompany = false;
                            });
                    }
                });
            },

            saveData() {

            },

            handleChangeFileCertificado(file) {
                handleFileSelect(file.raw);
            },

            contructSendObject() {
                return {};
            }
        }
    };
</script>
