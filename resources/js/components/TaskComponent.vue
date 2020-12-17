<style>
    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity 0.3s ease;
        overflow-y: auto;
    }
    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }
    .pac-container {
        z-index: 10000 !important;
    }
</style>
<template>
    <div class="container-fluid">
        <div v-if="showModalTask">
            <transition name="modal">
                <div class="modal-mask">
                    <div class="modal-wrapper">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="text text-primary">
                                        Crear Tarea
                                    </h4>
                                    <button type="button" class="close" @click="closeModal">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="task-name">Nombre:
                                                </label>
                                                <input
                                                    type="text"
                                                    v-model="task.name"
                                                    id="task-name"
                                                    class="form-control"
                                                    :class="!$v.task.name.$error ? '': 'is-invalid'">
                                                    <div v-if="submitted&&$v.task.name.$error">
                                                        <span class="badge badge-danger">campo obligatorio</span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="task-description">Descripción:
                                                    </label>
                                                    <textarea
                                                        type="text"
                                                        v-model="task.description"
                                                        id="task-description"
                                                        class="form-control"
                                                        :class="!$v.task.description.$error ? '': 'is-invalid'"></textarea>
                                                    <div v-if="submitted&&$v.task.description.$error">
                                                        <span class="badge badge-danger">campo obligatorio</span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>status:
                                                    </label>
                                                    <input type="checkbox" v-model="task.status" class="form-control">
                                                        <span :class="task.status? 'badge  badge-success': 'badge  badge-danger' ">{{task.status ? 'Completada': 'Pendiende'}}</span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-danger" type="button" @click="closeModal">
                                                <i class="fa fa-times"></i>
                                                Cancelar</button>
                                            <button v-if="editing==false" class="btn btn-success" @click="addTask">
                                                <i class="fa fa-plus"></i>
                                                Crear</button>
                                            <button v-else="v-else" class="btn btn-warning" @click="updateTask">
                                                <i class="fa fa-pencil"></i>
                                                Editar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">Tareas</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <button
                                        id="show-modal"
                                        class="btn btn-success"
                                        type="button"
                                        @click="openModal">
                                        <i class="fa fa-plus"></i>
                                        Agregar
                                    </button>
                                </div>
                                <vue-good-table
                                    theme="black-rhino"
                                    :columns="columns"
                                    :rows="tasks"
                                    :search-options="{
                                                enabled: true,
                                                placeholder: 'Buscar en la tabla',
                                            }"
                                    :pagination-options="{
                                    enabled: true,
                                    mode: 'records',
                                    perPage: 5,
                                    perPageDropdown: [3, 7, 9],
                                    dropdownAllowAll: true,
                                    setCurrentPage: 1,
                                    nextLabel: 'siguiente',
                                    prevLabel: 'anterior',
                                    rowsPerPageLabel: 'Filas por página',
                                    ofLabel: 'de',
                                    pageLabel: 'página', // for 'pages' mode
                                    allLabel: 'Todos',
                                }">

                                    <div v-if="loading" slot="emptystate">
                                        <div class="form-group text-center">
                                            <div class="fa-3x">
                                                <i class="fas fa-sync fa-spin"></i>
                                            </div>
                                            Cargando...
                                        </div>
                                    </div>
                                    <div v-else="v-else" slot="emptystate">
                                        <div class="form-group text-center">
                                            <div class="fa-3x">
                                                <i class="fas fa-sync fa-spin"></i>
                                            </div>
                                            Sin tareas para mostrar
                                        </div>
                                    </div>
                                    <template slot="table-row" slot-scope="props">
                                        <span v-if="props.column.field == 'activo'">
                                            <span :class="`badge ${props.row.status? 'badge-success' : 'badge-danger'}`">{{props.row.status? 'Completada' : 'Pendiente' }}</span>
                                        </span>
                                        <span v-else="v-else">
                                            {{props.formattedRow[props.column.field]}}
                                        </span>
                                        <span v-if="props.column.field == 'actions'">
                                            <div class="form-group text-center">
                                                <button class="btn btn-sm btn-warning" @click="editTask(props)">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" @click="deleteTask(props)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </span>
                                    </template>
                                </vue-good-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <script>
            import 'vue-good-table/dist/vue-good-table.css'
            import {VueGoodTable} from 'vue-good-table';
            import {required, email, minLength, sameAs} from "vuelidate/lib/validators";
            import Swal from 'sweetalert2';
            export default {
                data() {
                    return {
                        loading: false,
                        editing: false,
                        tasks: [],
                        task: {
                            name: "",
                            description: "",
                            status: false
                        },
                        showModalTask: false,
                        submitted: false,
                        columns: [
                            {
                                label: 'Nombre',
                                field: 'name',
                                sortable: true,
                                type: 'text'
                            }, {
                                label: 'Descripcion',
                                field: 'description',
                                type: 'text'
                            }, {
                                label: 'Estado',
                                field: 'activo',
                                type: 'button'
                            }, {
                                label: 'Acciones',
                                field: 'actions',
                                type: 'button'
                            }
                        ]
                    }
                },
                mounted() {
                    this.getTasks();
                },
                validations: {
                    task: {
                        name: {
                            required
                        },
                        description: {
                            required
                        },
                        status: {
                            required
                        }
                    }
                },
                methods: {
                    openModal() {
                        this.editing = false;
                        this.showModalTask = true;
                    },
                    getTasks() {
                        this.loading = true;
                        axios
                            .get('/tasks')
                            .then((response) => {
                                this.tasks = response.data;
                                this.loading = false;
                            })
                            .catch(function (error) {
                                // handle error
                                this.loading = true;
                                console.log("ocurrio un error: ", error);
                            });
                    },
                    addTask(e) {
                        this.submitted = true;
                        this
                            .$v
                            .$touch();
                        if (this.$v.$invalid) {
                            console.log("formulario invalido");
                            return;
                        }
                        //instacia afuera de la clase al adjuntar un archivo
                        let me = this;
                        let data = this.task;
                        axios
                            .post('/tasks', data)
                            .then(function (response) {
                                Swal.fire(
                                    {title: 'Tarea creada', icon: "success", timer: 2500, showConfirmButton: false}
                                );
                                me.cleanForm();
                                me.closeModal();
                                me.getTasks();
                            })
                            .catch(function (error) {
                                console.log('fallo!!', error);
                            });
                    },
                    setTask(task) {
                        this.task = task;
                    },
                    editTask(data) {
                        let task = data.row;
                        this.editing = true;
                        this.setTask(task);
                        this.showModalTask = true;
                    },
                    updateTask(e) {
                        this.submitted = true;
                        this
                            .$v
                            .$touch();
                        if (this.$v.$invalid) {
                            return;
                        }
                        let me = this;
                        let data = this.task;
                        axios
                            .put(`/tasks/${data.id}`, data)
                            .then(function (response) {
                                Swal.fire(
                                    {title: 'Tarea actualizada', icon: "success", timer: 2500, showConfirmButton: false}
                                );
                                me.cleanForm();
                                me.closeModal();
                                me.getTasks();
                            })
                            .catch(function (error) {
                                console.log('fallo!!', error);
                            });
                    },
                    deleteTask(data) {
                        const task = data.row;
                        Swal
                            .fire({
                                title: `¿Esta seguro que desea eliminar la tarea con nombre ${task.name}?`,
                                text: 'No podrá deshacer los cambios',
                                showCancelButton: true,
                                cancelButtonText: 'Cancelar',
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Si, eliminar!'
                            })
                            .then((result) => {
                                if (result.value) {
                                    axios
                                        .delete(`/tasks/${task.id}`)
                                        .then(response => {
                                            this.getTasks();
                                            this.cleanForm();
                                            Swal.fire(
                                                {title: 'Tarea eliminada', icon: "success", timer: 2500, showConfirmButton: false}
                                            );
                                        })
                                        .catch(e => {
                                            console.log('fallo!!', error);
                                        })
                                    }
                            })
                    },
                    closeModal() {
                        this.showModalTask = false;
                        this.editing = false;
                    },
                    cleanForm() {
                        this.task = {
                            name: "",
                            description: "",
                            status: false
                        };
                        this.submitted = false;
                    }
                },
                components: {
                    VueGoodTable
                }
            }
        </script>