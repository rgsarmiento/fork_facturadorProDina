<template>
    <div class="row">
        <div class="card col-md-12">
            <div class="card-header justify-content-center d-block">
                <p class="text-center text-muted mb-0 mt-2">Disponible para instalaciones con el script Docker desde Mayo 2020</p>
                <div class="text-center mt-2">
                    <el-button @click.prevent="start()" :loading="loading_submit">Iniciar Proceso</el-button>

                    <el-button @click.prevent="execComposer()" :loading="loading_submit">Composer Install</el-button>
                </div>
                <p class="text-center text-muted mb-0 mt-2">Versión actual: <span>{{version}}</span></p>
                <p class="text-right mb-0 mt-2"><a href="https://gitlab.com/b.mendoza/facturadorpro3/-/wikis/Script-Update-Docker" class="text-info" target="BLANK">Actualización alternativa</a></p>
            </div>
            <div class="card-body">

                <div v-if="content.status == true && content.step == 'updating'" id="response-content">

                    <h3>Obteniendo rama del repositorio</h3>
                    <el-progress :percentage="branch.percent"></el-progress>

                    <div v-if="branch.status == 'success'">
                        <h4>Rama actual: <strong>{{branch.name}}</strong></h4>
                        <span class="text-danger">{{branch.error}}</span><br>
                        <!-- <span class="text-danger">{{branch.status}}</span> -->
                        <hr>
                        <h3>Descargando actualización</h3>
                        <h4>Log: {{pull.content}}</h4>
                        <span class="text-danger">{{pull.error}}</span><br>
                        <!-- <span class="text-danger">{{pull.status}}</span> -->
                    </div>

                    <div v-if="pull.content.includes('Already up to date.')">
                        <hr>
                        <h3>El sistema está actualizado</h3>
                    </div>
                    <div v-if="pull.content.includes('Already up to date.') == false && pull.status == 'success'">
                        <hr>
                        <h3>Comandos Artisan</h3>
                    </div>

                    <div v-if="artisan.migrate.status == 'success'">
                        <hr>
                        <h3>Corriendo migraciones en administrador</h3>
                        <h4>Log: {{artisan.migrate.content}}</h4>
                        <span class="text-danger">{{artisan.migrate.error}}</span><br>
                        <!-- <span class="text-danger">{{artisan.migrate.status}}</span> -->
                    </div>

                    <div v-if="artisan.tenancy_migrate.status == 'success'">
                        <hr>
                        <h3>Corriendo migraciones en cliente</h3>
                        <h4>Log: {{artisan.tenancy_migrate.content}}</h4>
                        <span class="text-danger">{{artisan.tenancy_migrate.error}}</span><br>
                        <!-- <span class="text-danger">{{artisan.tenancy_migrate.status}}</span> -->
                    </div>

                    <div v-if="artisan.clear.status == 'success'">
                        <hr>
                        <h3>Eliminando Caché</h3>
                        <h4>Log: {{artisan.clear.content}}</h4>
                        <span class="text-danger">{{artisan.clear.error}}</span><br>
                        <!-- <span class="text-danger">{{artisan.clear.status}}</span> -->
                    </div>
                </div>

                <div v-if="content.status == true && content.step == 'composer'" id="response-content">

                    <div v-if="composer.install.status == 'success'">
                        <h3>Actualizando dependencias</h3>
                        <h4>Log:</h4>
                        <pre>
                            {{composer.install.content}}
                        </pre>
                        <span class="text-danger">{{composer.install.error}}</span><br>
                        <!-- <span class="text-danger">{{composer.install.status}}</span> -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
    import $ from 'jquery'

    export default {
        data() {
            return {
                headers: null,
                resource: 'auto-update',
                errors: {},
                form: {},
                loading_submit: false,
                version: '',
                content: {
                    status: false,
                },
                branch: {
                    name: '',
                    percent: 1,
                    error: '',
                    status: '',
                },
                pull: {
                    error: '',
                    status: '',
                    content: '',
                },
                artisan: {
                    error: '',
                    status: '',
                    migrate: {
                        error: '',
                        status: false,
                        content: '',
                    },
                    tenancy_migrate: {
                        error: '',
                        status: false,
                        content: '',
                    },
                    clear: {
                        error: '',
                        status: false,
                        content: '',
                    }
                },
                composer: {
                    install: {
                        error: '',
                        status: false,
                        content: '',
                    },
                    update: {
                        error: '',
                        status: false,
                        content: '',
                    },
                }
            }
        },
        created() {
            this.getVersion()
        },
        methods: {
            async start() {
                this.loading_submit = true
                this.initContent()
                this.content.status = true
                this.content.step = 'updating'
                await this.getBranch()
            },
            initContent() {
                this.content.status= false
                this.content.step= ''
                this.branch.name = ''
                this.branch.percent = 1
                this.branch.error = ''
                this.branch.status = false
                this.pull.error = ''
                this.pull.status = false
                this.artisan.error = ''
                this.artisan.status = false
                this.artisan.content = ''
                this.artisan.migrate
                this.artisan.migrate.error = ''
                this.artisan.migrate.status = false
                this.artisan.migrate.content = ''
                this.artisan.tenancy_migrate.error = ''
                this.artisan.tenancy_migrate.status = false
                this.artisan.tenancy_migrate.content = ''
                this.artisan.clear.error = ''
                this.artisan.clear.status = false
                this.artisan.clear.content = ''
                this.composer.install.error = ''
                this.composer.install.status = false
                this.composer.install.content = ''
                this.composer.update.error = ''
                this.composer.update.status = false
                this.composer.update.content = ''
            },
            getVersion() {
                this.$http.get(`/${this.resource}/version`)
                .then(response => {
                    if (response.data !== '') {
                        this.version = response.data
                    }
                }).catch(error => {
                    if (error.response.status !== 200) {
                        this.version.error = error.response.data.message
                    } else {
                        console.log(error)
                    }
                })
            },
            getBranch() {
                this.branch.percent = 40
                this.$http.get(`/${this.resource}/branch`)
                .then(response => {
                    this.branch.percent = 70
                    if (response.data !== '') {
                        this.branch.name = response.data
                        this.branch.percent = 100
                        if (response.status === 200) {
                            this.branch.status = 'success'
                        }
                        this.execPull()
                    }
                }).catch(error => {
                    if (error.response.status !== 200) {
                        this.branch.percent = 0
                        this.branch.error = error.response.data.message
                        this.branch.status = 'false'
                    } else {
                        console.log(error)
                    }
                })
            },
            execPull() {
                this.$http.get(`/${this.resource}/pull/${this.branch.name}`)
                .then(response => {
                    if (response.data !== '') {
                        this.pull.content = response.data
                        this.pull.percent = 100
                        if (response.status === 200) {
                            this.pull.status = 'success'
                        }
                        let pullContent = this.pull.content
                        if (pullContent.includes('Already up to date.') === true ) {
                            this.loading_submit = false
                        } else {
                            this.execArtisanMigrate()
                        }
                    }
                }).catch(error => {
                    this.pull.percent = 0
                    this.pull.error = 'no ha podido finalizar'
                    this.pull.status = 'false'
                    console.log(error)
                })
            },
            execArtisanMigrate() {
                this.$http.get(`/${this.resource}/artisan/migrate`)
                .then(response => {
                    if (response.data !== '') {
                        this.artisan.migrate.content = response.data
                        this.artisan.migrate.percent = 100
                        if (response.status === 200) {
                            this.artisan.migrate.status = 'success'
                            this.execArtisanMigrateTenant()
                        }
                    }
                }).catch(error => {
                    if (error.response.status !== 200) {
                        this.artisan.migrate.percent = 0
                        this.artisan.migrate.error = error.response.data.message
                        this.artisan.migrate.status = 'false'
                    } else {
                        console.log(error)
                    }
                })
            },
            execArtisanMigrateTenant() {
                this.$http.get(`/${this.resource}/artisan/migrate/tenant`)
                .then(response => {
                    if (response.data !== '') {
                        this.artisan.tenancy_migrate.content = response.data
                        this.artisan.tenancy_migrate.percent = 100
                        if (response.status === 200) {
                            this.artisan.tenancy_migrate.status = 'success'
                            this.execArtisanClear()
                        }
                    }
                }).catch(error => {
                    this.artisan.tenancy_migrate.percent = 0
                    this.artisan.tenancy_migrate.error = error
                    this.artisan.tenancy_migrate.status = false
                    console.log(error)
                })
            },
            execArtisanClear() {
                this.$http.get(`/${this.resource}/artisan/clear`)
                .then(response => {
                    if (response.data !== '') {
                        this.artisan.clear.content = response.data
                        this.artisan.clear.percent = 100
                        if (response.status === 200) {
                            this.artisan.clear.status = 'success'
                        }
                        this.loading_submit = false
                    }
                }).catch(error => {
                    this.artisan.clear.percent = 0
                    this.artisan.clear.error = error
                    this.artisan.clear.status = false
                    console.log(error)
                })
            },
            execComposer() {
                this.initContent()
                this.loading_submit = true
                this.content.status = true
                this.content.step = 'composer'
                this.$http.get(`/${this.resource}/composer/install`)
                .then(response => {

                    if (response.data !== '') {
                        this.composer.install.content = response.data
                        this.composer.install.percent = 100
                        if (response.status === 200) {
                            this.composer.install.status = 'success'
                        }
                    }
                }).catch(error => {
                    if (error.response.status !== 200) {
                        this.composer.install.percent = 0
                        this.composer.install.error = error.response.data.message
                        this.composer.install.status = 'false'
                    } else {
                        console.log(error)
                    }
                })

                this.loading_submit = false
            }
        }
    }
</script>
