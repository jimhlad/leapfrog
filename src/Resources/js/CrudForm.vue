<template>
    <div class="crud-form">
        <p>Start off by choosing an entity name: 
            <span class="clear-form">
                <a v-on:click="clearForm">clear form</a>
            </span>
        </p>
        <div class="well">
            <div class="form-group">
                <label for="entity_name">Entity Name</label>
                <input type="text" class="form-control" placeholder="e.g. Truck" v-model="entity_name" />
            </div>
        </div>
        <div v-if="entity_name" class="entity-fields">
            <p>What fields does the {{entity_name}} have?</p>
            <div class="well">
                <div v-for="(field, index) in fields" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="field_name">Field Name</label>
                            <input type="text" class="form-control" placeholder="e.g. name" v-model="field.name" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="field_type">Field Type</label>
                            <select class="form-control" v-model="field.type">
                                <option value="string">String</option>
                                <option value="text">Text</option>
                                <option value="integer">Integer</option>
                                <option value="unsignedinteger">Unsigned Integer</option>
                                <option value="float">Float</option>
                                <option value="date">Date</option>
                                <option value="datetime">Date Time</option>
                                <option value="boolean">Boolean</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="field_default">Options</label>
                            <div class="check-inline">
                                <input type="checkbox" value="fillable" v-model="field.options"> <span>Fillable</span>
                                <input type="checkbox" value="nullable" v-model="field.options"> <span>Nullable</span>
                                <input type="checkbox" value="index" v-model="field.options"> <span>Index</span>
                                <input type="checkbox" value="searchable" v-model="field.options"> <span>Searchable</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="field_default">Actions</label>
                            <p><a v-on:click="removeEntityField(index)">Remove</a></p>
                        </div>
                    </div>
                </div>
                <p><a v-on:click="addEntityField">Add field</a></p>
            </div>
        </div>
        <div v-if="entity_name" class="entity-relations">
            <p>What relations should the {{entity_name}} have?</p>
            <div class="well">
                <div v-for="(relation, index) in relations" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="relation_name">Relation Name</label>
                            <input type="text" class="form-control" placeholder="e.g. drivers" v-model="relation.name" />
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="relation_class">Relation Class</label>
                            <input type="text" class="form-control" placeholder="e.g. Driver::class" v-model="relation.class" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="relation_default">Actions</label>
                            <p><a v-on:click="removeEntityRelation(index)">Remove</a></p>
                        </div>
                    </div>
                </div>
                <p><a v-on:click="addEntityRelation">Add relation</a></p>
            </div>
        </div>
        <div v-if="entity_name" class="entity-files">
            <p>Which files do we want to create? 
                <span class="update-paths">
                    <a v-if="!show_update_paths" v-on:click="toggleShowUpdatePaths">customize paths</a>
                    <a v-else v-on:click="toggleShowUpdatePaths">hide</a>
                </span>
            </p>
            <div v-if="show_update_paths" class="well">
                <div class="form-group">
                    <label for="field_name">Models Path</label>
                    <input type="text" class="form-control" v-model="paths.models_path" />
                </div>
                <div class="form-group">
                    <label for="field_name">Controllers Path</label>
                    <input type="text" class="form-control" v-model="paths.controllers_path" />
                </div>
                <div class="form-group">
                    <label for="field_name">Services Path</label>
                    <input type="text" class="form-control" v-model="paths.services_path" />
                </div>
                <div class="form-group">
                    <label for="field_name">Requests Path</label>
                    <input type="text" class="form-control" v-model="paths.requests_path" />
                </div>
                <div class="form-group">
                    <label for="field_name">Migrations Path</label>
                    <input type="text" class="form-control" v-model="paths.migrations_path" />
                </div>
            </div>
            <div class="well">
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
                    <label><input type="checkbox" value="migration" v-model="files"> {{paths.migrations_path}}<strong>xxxx_xx_xx_xxxxxx_create_{{entityNameSnakeCase}}_table.php</strong></label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary pull-right">Okay, let's go!</button>
    </div>
</template>

<script>
    export default {
        props: ['models_path', 'controllers_path', 'services_path', 'requests_path', 'migrations_path'],
        data: function () {
            return initialState(this.models_path, this.controllers_path, this.services_path, this.requests_path, this.migrations_path);
        },
        computed: {
            entityNameSnakeCase() {
                return this.entity_name.toLowerCase();
            }
        },
        methods: {
            addEntityField() {
                Vue.set(this.fields, this.fields.length, {
                    name: '',
                    type: 'string',
                    options: ['fillable']
                });
            },
            addEntityRelation() {
                Vue.set(this.relations, this.relations.length, {
                    name: '',
                    type: 'BelongsTo',
                    class: ''
                });
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
            clearForm() {
                Object.assign(this.$data, initialState(this.models_path, this.controllers_path, this.services_path, this.requests_path, this.migrations_path));
            }
        }
    }
    function initialState(my_models_path, my_controllers_path, my_services_path, my_requests_path, my_migrations_path) {
        return {
            show_update_paths: false,
            entity_name: '',
            paths: {
                models_path: my_models_path,
                controllers_path: my_controllers_path,
                services_path: my_services_path,
                requests_path: my_requests_path,
                migrations_path: my_migrations_path
            },
            fields: [{
                name: '',
                type: 'string',
                options: ['fillable']
            }],
            relations: [],
            files: ['model', 'controller', 'service', 'createrequest', 'updaterequest', 'migration']
        }
    }
</script>
