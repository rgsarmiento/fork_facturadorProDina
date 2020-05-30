<template>
  <main class="content">
    <div class="container-fluid">
      <v-app id="configuration">
        <v-container>
          <v-layout row wrap>
            <v-flex lg12>
              <v-card class="mb-2">
                <v-list>
                  <v-form>
                    <v-container>
                      <v-layout row wrap>
                        <v-flex xs12 sm12 md12 lg12 xl12>
                          <h3>Datos de la Compañia</h3>
                        </v-flex>
                        <v-flex xs11 sm11 md5 lg3 xl3>
                          <v-text-field
                            v-model="company.identification_number"
                            label="Número de identificación"
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs2 sm2 md2 lg2 xl2>
                          <v-text-field v-model="company.dv" label="Dv" disabled></v-text-field>
                        </v-flex>
                        <v-flex xs11 sm11 md5 lg3 xl3>
                          <v-text-field
                            v-model="company.merchant_registration"
                            label="Registro mercantil"
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field v-model="company.address" label="Direccion" disabled></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field v-model="company.phone" label="Telefono" disabled></v-text-field>
                        </v-flex>
                        <!-- <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field v-model="company.language.name" label="Lenguaje" disabled></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field v-model="company.tax.name" label="Impuesto" disabled></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field v-model="company.country.name" label="Pais" disabled></v-text-field>
                        </v-flex> -->
                        <!-- <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field
                            v-model="company.type_document_identification.name"
                            label="Tipo Documentacion"
                            disabled
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field
                            v-model="company.type_operation.name"
                            label="Tipo Operacion"
                            disabled
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field
                            v-model="company.type_environment.name"
                            label="Tipo Entorno"
                            disabled
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field
                            v-model="company.type_currency.name"
                            label="Tipo Moneda"
                            disabled
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field
                            v-model="company.type_organization.name"
                            label="Tipo Organización "
                            disabled
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field
                            v-model="company.municipality.name"
                            label="Municipio "
                            disabled
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field
                            v-model="company.type_liability.name"
                            label="Tipo Responsabilidad "
                            disabled
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md6 lg4 xl4>
                          <v-text-field
                            v-model="company.type_regime.name"
                            label="Tipo Regimen "
                            disabled
                          ></v-text-field>
                        </v-flex> -->
                      </v-layout>
                    </v-container>
                  </v-form>
                </v-list>
                <v-card-actions>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-flex> 
 
          </v-layout>

          <textarea hidden id="base64" rows="5"></textarea>
        </v-container>
      </v-app>
    </div>
  </main>
</template>

<script>
function handleFileSelect(file) {
  var f = file; //evt.target.files[0]; // FileList object
  var reader = new FileReader();
  // Closure to capture the file information.
  reader.onload = (function(theFile) {
    return function(e) {
      var binaryData = e.target.result;
      //Converting Binary Data to base 64
      var base64String = window.btoa(binaryData);
      //showing file converted to base64
      document.getElementById("base64").value = base64String;
      console.log(
        "File converted to base64 successfuly!\nCheck in Textarea hidden"
      );
      //return base64String;
    };
  })(f);
  // Read in the image file as a data URL.
  reader.readAsBinaryString(f);
}

// import Helper from "../../../mixins/Helper";

export default {
  // mixins: [Helper],
  props: {
    // route: {
    //   required: true
    // }
  },
  data: () => ({
    //errors: {},
    typeDocuments: [
      { id: 1, name: "Factura de Venta Nacional" },
      { id: 2, name: "Factura de Exportación" },
      { id: 3, name: "Factura de Contingencia" },
      { id: 4, name: "Nota Crédito" },
      { id: 5, name: "Nota Débito" },
      { id: 6, name: "ZIP" }
    ],
    fileCertificado: "",
    typeIdentityDocuments: [],
    loadingCompany: false,
    loadingOther: false,
    typeObligations: [],
    type_documents: {},
    // typeDocuments: [],
    versionUbls: [],
    typeRegimes: [],
    departments: [],
    currencies: [],
    countries: [],
    ambients: [],
    cities: [],
    company:{}
  }),
  mounted() {
    if (window.File && window.FileReader && window.FileList && window.Blob) {
      console.log("ok.");
    } else {
      alert("The File APIs are not fully supported in this browser.");
    }
    //this.company.resolution_date = new Date().toISOString().substr(0, 10);
    //this.company.technical_key = new Date().toISOString().substr(0, 10);

    /*axios
      .post(`${this.route}All`)
      .then(response => {
        this.$setLaravelMessage(response.data);

        this.typeIdentityDocuments = response.data.typeIdentityDocuments;
                this.typeDocuments = response.data.typeDocuments;
                this.typeObligations = response.data.typeObligations;
                this.typeRegimes = response.data.typeRegimes;
                this.versionUbls = response.data.versionUbls;
                this.currencies = response.data.currencies;
                this.countries = response.data.countries;
                this.ambients = response.data.ambients;

        //  if (this.company.country_id != null) this.departmentss(true);
        //if (this.company.department_id != null) this.citiess(true);
      })
      .catch(error => {
        this.$setLaravelValidationErrorsFromResponse(error.response.data);
        this.$setLaravelErrors(error.response.data);
      })
      .then(() => {});*/
  },
  methods: {
    /*aca()
    {
      handleFileSelect('asdasd asd')
    },*/
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
          //  let url = this.url(scope, model, models, modelObject);
          this.loadingCompany = true;

          axios
            .post(`${this.route}/storeServiceCompanie`, this.company)
            .then(response => {
              // console.log(response.data, 'mio')
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
      // this.company.certificate64 = document.getElementById("base64").value;

      this.$validator.validateAll(scope).then(valid => {
        if (valid) {
          this.loadingCompany = true;

          axios
            .post(`${this.route}/storeServiceCompanieSoftware`, this.software)
            .then(response => {
              // console.log(response.data, 'mio')
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

    validateCertificate(
      scope,
      model = null,
      models = null,
      modelObject = null
    ) {
      this.$validator.validateAll(scope).then(valid => {
        if (valid) {
          this.certificate.certificate64 = document.getElementById(
            "base64"
          ).value;
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

    saveData() {},
    handleChangeFileCertificado(file) {
      // this.fileCertificado = file.raw;
      handleFileSelect(file.raw);
      //console.log(dato)
    },
    contructSendObject() {
      return {};
    }
  }
};
</script>
