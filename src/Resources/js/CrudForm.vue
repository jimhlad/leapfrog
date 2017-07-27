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
            <p>What model fields should a {{entity_name}} have?</p>
            <div class="well">
                <div v-for="(field, index) in fields" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="field_name">Field Name</label>
                            <input type="text" class="form-control" placeholder="e.g. description" v-model="field.name" />
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
                                <option value="belongsto">BelongsTo</option>
                                <option value="hasone">HasOne</option>
                                <option value="hasmany">HasMany</option>
                                <option value="belongstomany">BelongsToMany</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="field_options">Options</label>
                            
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="field_default">Other</label>
                            
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="field_default">Actions</label>
                            <a v-on:click="removeEntityField(index)">Remove</a>
                        </div>
                    </div>
                </div>
                <p><a v-on:click="addEntityField">Add field</a></p>
            </div>
        </div>
        <div v-if="entity_name" class="entity-files">
            <p>What files do we want to create?</p>
            <div class="well">
                <div class="checkbox"><label><input type="checkbox"> {{entity_name}}Controller</label></div>
                <div class="checkbox"><label><input type="checkbox"> {{entity_name}}Service</label></div>
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
                    type: 'string'
                }]
            }
        },
        methods: {
            addEntityField() {
                Vue.set(this.fields, this.fields.length, {
                    name: '',
                    type: 'string'
                });
            },
            removeEntityField(index) {
                this.fields.splice(index, 1);
            }
        }
    }
</script>
