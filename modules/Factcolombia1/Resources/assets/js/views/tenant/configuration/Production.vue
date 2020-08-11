<template>
  <main class="content">
    <div class="container-fluid">
      <v-app id="configuration">
        <v-container>
          <v-layout row wrap>
            <v-flex lg12>
              <v-card class="mb-2">
                <v-list>
                  <v-form data-vv-scope="production">
                    <v-container>
                      <v-layout row wrap>
                        <v-flex xs12 sm12 md12 lg12 xl12>
                          <h3>Cambiar Ambiente de Operacion - (HABILITACION - PRODUCCION)</h3>
                        </v-flex>
                        <v-flex xs12 sm12 md12 lg12 xl12>

                            <v-textarea
                                v-model="production.technicalkey"
                                rows=20
                            >
                                <template v-slot:label>
                                    <div>
                                        ResponseDian <small>(Seleccione de aqui la llave tecnica.)</small>
                                    </div>
                                </template>
                            </v-textarea>
<!--                          <v-text-field
                            v-model="production.technicalkey"
                            label="Llave Tecnica de la Resolucion de Facturacion."
                          ></v-text-field>    -->
                        </v-flex>
                      </v-layout>
                    </v-container>
                  </v-form>
                </v-list>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn
                    class="bee darker text-white no-decoration"
                    :loading="loadingCompany"
                    @click="validateProduction('H')"
                  >Pasar a Habilitación.</v-btn>
                  <v-btn
                    class="bee darker text-white no-decoration"
                    :loading="loadingCompany"
                    @click="validateProduction('P')"
                  >Pasar a Producción.</v-btn>
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
import Helper from "../../../mixins/Helper";

export default {
  mixins: [Helper],
  props: {
    route: {
      required: true
    }
  },

  data: () => ({
     loadingCompany: false,
     production: { technicalkey: ''},
  }),

  methods: {
    validateProduction(environment) {
        this.loadingCompany = true;
        axios
            .post(`${this.route}/changeEnvironmentProduction/${environment}`)
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
        if(environment == 'P'){
           this.loadingCompany = true;
            axios
                .post(`${this.route}/queryTechnicalKey`)
                .then(response => {
//                    this.$setLaravelMessage(response.data);
                    this.production.technicalkey = JSON.stringify(response.data, null, 2)
                })
                .catch(error => {
                    this.$setLaravelValidationErrorsFromResponse(error.response.data);
                    this.$setLaravelErrors(error.response.data);
                })
                .then(() => {
                    this.loadingCompany = false;
                });
        }
        else
            this.production.technicalkey = "No se pueden consultar claves tecnicas para ambiente de HABILITACION."
    }
  }
};
</script>
