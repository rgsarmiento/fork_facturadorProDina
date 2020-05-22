<template>
    <v-app id="client_form" style="height: 0px;">
        <v-layout row justify-center>
            <v-dialog v-model="dialogImport" persistent max-width="700" transition="dialog-bottom-transition">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{title}}</span>
                    </v-card-title>
                    <v-list three-line subheader>
                        <v-form data-vv-scope="form">
                            <v-container>
                                <v-layout row wrap>
                                    <v-flex xs12 sm12 md12 lg12>
                                        <a :href="`${route}/formatImport`" class="grey lighten-2 btn btn-sm"><i class="fa fa-cloud-download-alt"></i> Descargar Formato</a>
                                    </v-flex>
                                    <v-flex class="text-xs-center text-sm-center text-md-center text-lg-center" xs12 sm12 md12 lg12>
                                        <v-text-field v-model="form.file" v-validate="'required|max:50'" :error-messages="errors.collect('form.file')" data-vv-name="file" :counter="50" prepend-icon="attach_file" label="Seleccione el archivo *" @click="pickFile"></v-text-field>
                                        <input type="file" style="display: none" ref="file" accept=".xlsx,.xls" @change="onFilePicked">
                                    </v-flex>
                                </v-layout>
                            </v-container>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="warning" flat @click="close">Cerrar</v-btn>
                                <v-btn color="bee" flat :loading="loading" @click="validate('form')" class="text-white">Importar</v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-list>
                </v-card>
            </v-dialog>
        </v-layout>
    </v-app>
</template>

<script>
    export default {
        props: {
            title: {
                required: true
            },
            route: {
                required: true
            },
            dialogImport: {
                required: true
            }
        },
        data: () => ({
            loading: false,
            form: {}
        }),
        methods: {
            cleanForm() {
                this.$set(this.form, 'file', '');
                
                this.$validator.reset();
            },
            close() {
                this.cleanForm();
                
                this.$refs.file.value = '';
                
                this.$emit('update:dialogImport', false);
            },
            pickFile () {
                this.$refs.file.click();
            },
            onFilePicked(e) {
                let files = e.target.files;
                
                if (files[0] !== undefined) {
                    this.$set(this.form, 'file', files[0].name);
                    
                    return;
                }
                
                this.$set(this.form, 'file', '');
            },
            validate(scope) {
                this.$validator.validateAll(scope).then(valid => {
                    if (valid) {
                        this.loading = true;
                        
                        let Data = new FormData(this.company);
                        
                        if (this.$refs.file.files.length > 0) Data.append('file', this.$refs.file.files[0], this.$refs.file.files[0].name);
                        
                        Data.append('_method', 'PUT');
                        
                        axios.post(`${this.route}/import/excel`, Data).then(response => {
                            this.$setLaravelMessage(response.data);
                            
                            if (response.data.success) {
                                this.$emit('refresh');
                                
                                this.close();
                            };
                        }).catch(error => {
                            this.$setLaravelValidationErrorsFromResponse(error.response.data);
                            this.$setLaravelErrors(error.response.data);
                        }).then(() => {
                            this.loading = false;
                        });
                    }
                });
            }
        }
    }
</script>
