<template>
    <div class="crud-form">
        <p>Start off by choosing an entity name:</p>
        <div class="well">
            <div class="form-group">
                <label for="entity_name">Entity Name</label>
                <input type="text" class="form-control" placeholder="e.g. Truck" v-model="entity_name" />
            </div>
        </div>
        <div v-if="entity_name" class="entity-fields">
            <p>What database fields should a {{entity_name}} have?</p>
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
                                <input type="checkbox" value="nullable" v-model="field.options"> <span>Nullable</span>
                                <input type="checkbox" value="unsigned" v-model="field.options"> <span>Unsigned</span>
                                <input type="checkbox" value="index" v-model="field.options"> <span>Index</span>
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
        <div v-if="entity_name" class="entity-files">
            <p>What files do we want to create?</p>
            <div class="well">
                <div class="checkbox">
                    <label><input type="checkbox" value="model" v-model="files"> {{entity_name}}.php</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="controller" v-model="files"> {{entity_name}}Controller.php</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="service" v-model="files"> {{entity_name}}Service.php</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="createrequest" v-model="files"> {{entity_name}}CreateRequest.php</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="updaterequest" v-model="files"> {{entity_name}}UpdateRequest.php</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="migration" v-model="files"> xxxx_xx_xx_xxxxxx_create_{{entityNameSnakeCase}}_table</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary pull-right">Okay, let's go!</button>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                entity_name: '',
                fields: [{
                    name: '',
                    type: 'string',
                    options: []
                }],
                files: ['model', 'controller', 'service', 'createrequest', 'updaterequest', 'migration']
            }
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
                    options: []
                });
            },
            removeEntityField(index) {
                this.fields.splice(index, 1);
            }
        }
    }
</script>
