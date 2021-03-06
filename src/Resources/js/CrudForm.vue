<template>
    <form>
        <p>Start off by choosing an entity name: 
            <span class="clear-form">
                <a v-on:click="clearForm">clear form</a>
            </span>
        </p>
        <div class="well">
            <div class="form-group">
                <label for="entity_name">Entity Name</label>
                <input type="text" class="form-control" placeholder="e.g. Truck" v-model="entity_name" v-on:blur="removeSpaceCharacters()" />
            </div>
        </div>
        <div v-if="entity_name" class="entity-fields">
            <p>What fields does the {{entity_name}} have?</p>
            <div class="well">
                <div v-for="(field, index) in fields" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="field_name">Field Name</label>
                            <input v-if="index === 0" type="text" class="form-control" placeholder="e.g. name" v-model="field.name" />
                            <input v-else v-focus type="text" class="form-control" placeholder="e.g. name" v-model="field.name" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="field_type">Field Type</label>
                            <select class="form-control" v-model="field.type">
                                <option value="string">String</option>
                                <option value="text">Text</option>
                                <option value="integer">Integer</option>
                                <option value="float">Float</option>
                                <option value="date">Date</option>
                                <option value="dateTime">Date Time</option>
                                <option value="boolean">Boolean</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="field_default">Options</label>
                            <div class="check-inline">
                                <input type="checkbox" value="fillable" v-model="field.options"> <span>Fillable</span>
                                <input type="checkbox" value="nullable" v-model="field.options"> <span>Nullable</span>
                                <input type="checkbox" value="hidden" v-model="field.options"> <span>Hidden</span>
                                <br/>
                                <span>
                                    <input type="checkbox" value="index" v-model="field.options"> <span>Index</span>
                                </span>
                                <span v-if="['string'].indexOf(field.type) !== -1">
                                    <input type="checkbox" value="unique" v-model="field.options"> <span>Unique</span>
                                </span>
                                <span v-if="['integer'].indexOf(field.type) !== -1">
                                    <input type="checkbox" value="unsigned" v-model="field.options"> <span>Unsigned</span>
                                </span>
                                <span v-if="['integer'].indexOf(field.type) !== -1">
                                    <input type="checkbox" value="foreign" v-model="field.options"> <span>Foreign</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-10">
                                <div v-if="field.type === 'string'" class="form-group">
                                    <label for="field_custom">
                                        Selectable Values (Optional)
                                    </label>
                                    <input type="text" class="form-control" placeholder="e.g. Active,In Progress" v-model="field.custom" />
                                </div>
                                <div v-else-if="field.type === 'boolean'" class="form-group">
                                    <label for="field_custom">
                                        Default (Optional)
                                    </label>
                                    <input type="text" class="form-control" placeholder="e.g. false" v-model="field.custom" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a v-on:click="removeEntityField(index)">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <p><a v-on:click="addEntityField" v-on:keypress="addEntityFieldKeyPress" tabindex="0">Add field</a></p>
            </div>
        </div>
        <div v-if="entity_name" class="entity-relations">
            <p>What relations should the {{entity_name}} have?</p>
            <div class="well">
                <div v-for="(relation, index) in relations" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="relation_name">Relation Name</label>
                            <input v-focus type="text" class="form-control" placeholder="e.g. driver" v-model="relation.name" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="relation_type">Relation Type</label>
                            <select class="form-control" v-model="relation.type">
                                <option value="belongsto">BelongsTo</option>
                                <option value="hasone">HasOne</option>
                                <option value="hasmany">HasMany</option>
                                <option value="belongstomany">BelongsToMany</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="relation_class">Model Path</label>
                            <input type="text" class="form-control" v-model="relation.model_path" v-on:blur="addTrailingSlashesRelation(index)" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="relation_class">Model Name</label>
                                    <input type="text" class="form-control" placeholder="e.g. Driver" v-model="relation.model_name" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a v-on:click="removeEntityRelation(index)">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <p><a v-on:click="addEntityRelation" v-on:keypress="addEntityRelationKeyPress" tabindex="0">Add relation</a></p>
            </div>
        </div>
        <div v-if="entity_name" class="entity-files">
            <p>Which files do we want to create/update? 
                <span class="update-paths">
                    <a v-if="!show_update_paths" v-on:click="toggleShowUpdatePaths">customize paths</a>
                    <a v-else v-on:click="toggleShowUpdatePaths">hide</a>
                </span>
            </p>
            <div v-if="show_update_paths" class="well">
                <div class="form-group">
                    <label for="field_name">Models Path</label>
                    <input type="text" class="form-control" v-model="paths.models_path" v-on:blur="addTrailingSlashes()" />
                </div>
                <div class="form-group">
                    <label for="field_name">Controllers Path</label>
                    <input type="text" class="form-control" v-model="paths.controllers_path" v-on:blur="addTrailingSlashes()" />
                </div>
                <div class="form-group">
                    <label for="field_name">Services Path</label>
                    <input type="text" class="form-control" v-model="paths.services_path" v-on:blur="addTrailingSlashes()" />
                </div>
                <div class="form-group">
                    <label for="field_name">Requests Path</label>
                    <input type="text" class="form-control" v-model="paths.requests_path" v-on:blur="addTrailingSlashes()" />
                </div>
                <div class="form-group">
                    <label for="field_name">Views Path</label>
                    <input type="text" class="form-control" v-model="paths.views_path" v-on:blur="addTrailingSlashes()" />
                </div>
            </div>
            <div class="well">
                <div class="checkbox">
                    <label><input type="checkbox" value="route" v-model="files"> routes/<strong>web.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="model" v-model="files"> {{paths.models_path}}<strong>{{entity_name}}.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="controller" v-model="files"> {{paths.controllers_path}}<strong>{{entity_name}}Controller.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="service" v-model="files"> {{paths.services_path}}<strong>{{entity_name}}Service.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="createrequest" v-model="files"> {{paths.requests_path}}<strong>{{entity_name}}CreateRequest.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="updaterequest" v-model="files"> {{paths.requests_path}}<strong>{{entity_name}}UpdateRequest.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="migration" v-model="files"> database/migrations/<strong>xxxx_xx_xx_xxxxxx_create_xxxxxx_table.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="indexview" v-model="files"> {{paths.views_path}}<strong>{{entityNameSnakeCase}}/index.blade.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="createview" v-model="files"> {{paths.views_path}}<strong>{{entityNameSnakeCase}}/create.blade.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="editview" v-model="files"> {{paths.views_path}}<strong>{{entityNameSnakeCase}}/edit.blade.php</strong></label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="formconfig" v-model="files"> config/forms/<strong>{{entityNameSnakeCase}}.php</strong></label>
                </div>
            </div>
        </div>
        <div v-if="entity_name" class="submit-button-row">
            <button v-on:click="submitForm" type="button" class="btn btn-primary pull-right" :disabled="loading">
                <span v-if="!loading">Okay, let's go!</span>
                <span v-else>Hopping around, eating flies...</span>
            </button>
        </div>        
        <div v-if="generate_api_output" v-html="generate_api_output" class="alert alert-info"></div>
    </form>
</template>

<script>
    import axios from 'axios';

    Vue.directive('focus', {
        inserted: function (el) {
            el.focus();
        }
    });

    export default {
        props: ['models_path', 'controllers_path', 'services_path', 'requests_path', 'views_path', 'generate_url'],
        data: function () {
            return initialState(this.models_path, this.controllers_path, this.services_path, this.requests_path, this.views_path);
        },
        computed: {
            entityNameSnakeCase() {
                return _.snakeCase(this.entity_name);
            }
        },
        methods: {
            addEntityField() {
                let field = Vue.set(this.fields, this.fields.length, {
                    name: '',
                    type: 'string',
                    options: ['fillable'],
                    custom: ''
                });
            },
            addEntityRelation() {
                Vue.set(this.relations, this.relations.length, {
                    name: '',
                    type: 'belongsto',
                    model_path: this.paths.models_path,
                    model_name: ''
                });
            },
            addEntityFieldKeyPress(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    this.addEntityField();
                }
            },
            addEntityRelationKeyPress(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    this.addEntityRelation();
                }
            },
            removeEntityField(index) {
                this.fields.splice(index, 1);
            },
            removeEntityRelation(index) {
                this.relations.splice(index, 1);
            },
            toggleShowUpdatePaths() {
                this.show_update_paths = !this.show_update_paths;
            },
            addTrailingSlashes() {
                if (this.paths.models_path.substr(-1) != '/') this.paths.models_path += '/';
                if (this.paths.controllers_path.substr(-1) != '/') this.paths.controllers_path += '/';
                if (this.paths.services_path.substr(-1) != '/') this.paths.services_path += '/';
                if (this.paths.requests_path.substr(-1) != '/') this.paths.requests_path += '/';
                if (this.paths.views_path.substr(-1) != '/') this.paths.views_path += '/';
            },
            addTrailingSlashesRelation(index) {
                if (this.relations[index].model_path.substr(-1) != '/') this.relations[index].model_path += '/';
            },
            removeSpaceCharacters() {
                this.entity_name = this.entity_name.split(' ').join('');
            },
            clearForm() {
                Object.assign(this.$data, initialState(this.models_path, this.controllers_path, this.services_path, this.requests_path, this.views_path));
            },
            submitForm() {
                this.loading = true;
                this.generate_api_output = '';
                axios.post(this.generate_url, {
                    entity_name: this.entity_name,
                    paths: this.paths,
                    fields: this.fields,
                    relations: this.relations,
                    files: this.files
                })
                .then(response => {
                    this.loading = false;
                    this.generate_api_output = response.data.join('<br/>');
                })
                .catch(e => {
                    this.loading = false;
                    this.generate_api_output = e;
                })
            }
        }
    }
    function initialState(my_models_path, my_controllers_path, my_services_path, my_requests_path, my_views_path) {
        return {
            loading: false,
            show_update_paths: false,
            generate_api_output: '',
            entity_name: '',
            paths: {
                models_path: my_models_path,
                controllers_path: my_controllers_path,
                services_path: my_services_path,
                requests_path: my_requests_path,
                views_path: my_views_path
            },
            fields: [{
                name: 'name',
                type: 'string',
                options: ['fillable'],
                custom: ''
            }],
            relations: [],
            files: ['route', 'model', 'controller', 'service', 'createrequest', 'updaterequest', 'migration', 'indexview', 'createview', 'editview', 'formconfig']
        }
    }
</script>
