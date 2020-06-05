<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Software</h3>
        </div>
        <div class="tab-content">
            <div class="software">
                <form autocomplete="off">
                    <div class="form-body">
                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.id_software}">
                                    <label class="control-label">Id Software *</label>
                                    <el-input
                                        v-model="software.id_software"
                                        placeholder="Introduzca el Id Software asignado por la DIAN."
                                        :disabled="false"
                                        autofocus>
                                    </el-input>
                                    <small class="form-control-feedback" v-if="errors.id_software" v-text="errors.id_software[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.pin_software}">
                                    <label class="control-label">Pin Software *</label>
                                    <el-input
                                        v-model="software.pin_software"
                                        placeholder="Digite el pin del Software"
                                        :disabled="false"
                                        maxlength="5"
                                        show-word-limit>
                                    </el-input>
                                    <small class="form-control-feedback" v-if="errors.pin_software" v-text="errors.pin_software[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.test_id}">
                                    <label class="control-label">Test Set ID *</label>
                                    <el-input
                                        v-model="software.test_id"
                                        placeholder="Introduzca el codigo del Set de Pruebas para habilitacion."
                                        :disabled="false">
                                    </el-input>
                                    <small class="form-control-feedback" v-if="errors.test_id" v-text="errors.test_id[0]"></small>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions text-right mt-4">
                            <el-button
                                type="primary"
                                :loading="loadingSoftware"
                                @click="validateSoftware()"
                                >Guardar</el-button>
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
            errors: {
            },
            software: {
                id_software: '12345-67890-09876-54321',
                pin_software: '12345',
                test_id: '0abcde-1fghij-2klmno-3pqrstu'
            },
            loadingSoftware: false,
        }),

        mounted() {
            this.errors = {
            }
        },

        methods: {
            validateSoftware() {
                this.loadingSoftware = true
                this.$http.post(`/storeServiceCompanieSoftware`, this.software)
                    .then(response => {
                        console.log(response)
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data)
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loadingSoftware = false
                    })
            },
            WriteFile(texto) {
               var fso  = new CreateObject("Scripting.FileSystemObject"); 
               var fh = fso.CreateTextFile("C:\\FRS\\DEBUG.TXT", true); 
               fh.WriteLine(JSON.stringify(texto)); 
               fh.Close(); 
            }
        }
    };
</script>
