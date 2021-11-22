<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Configuración</span></li>
                <li><span class="text-muted">Avanzado</span></li>
            </ol>
        </div>
        <div class="card card-dashboard border">
            <div class="card-body">
                <template>
                    <form autocomplete="off">
                        <el-tabs v-model="activeName">
                            <el-tab-pane class="mb-3"
                                         name="first">
                                <span slot="label"><h3>Nómina</h3></span>
                                <div class="row">
                                    
                                    <div class="col-md-4 mt-4">
                                        <label class="control-label">Salario mínimo</label>
                                        <div class="form-group">
                                            <el-input-number v-model="form.minimum_salary" :min="0" controls-position="right"
                                                             @change="submit"></el-input-number>

                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 mt-4">
                                        <label class="control-label">Subsidio de transporte</label>
                                        <div class="form-group">
                                            <el-input-number v-model="form.transportation_allowance" :min="0" controls-position="right"
                                                             @change="submit"></el-input-number>
                                        </div>
                                    </div>

                                </div> 
                            </el-tab-pane> 
                        </el-tabs> 
                    </form>
                </template>
            </div>
        </div>
    </div>
</template>

<script>


export default {
    data() {
        return {
            loading_submit: false,
            resource: 'co-advanced-configuration',
            errors: {},
            form: {},
            activeName: 'first'
        }
    },
    created() {
        this.getRecord()
    },
    methods: {
        async getRecord() {

            await this.$http.get(`/${this.resource}/record`).then(response => {
                this.form = response.data.data
            });
        },
        initForm() {
            this.errors = {};
            this.form = {
                minimum_salary: 0,
                transportation_allowance: 0,
            };
        },
        submit() {

            this.loading_submit = true;

            this.$http.post(`/${this.resource}`, this.form).then(response => {
                let data = response.data;
                if (data.success) {
                    this.$message.success(data.message);
                } else {
                    this.$message.error(data.message);
                }

            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    console.log(error);
                }
            }).then(() => {
                this.loading_submit = false;
            });
        },
    }
}
</script>
