<template>
    <div class="panel-1">
        <div id="form-options">
            <form v-on:submit.prevent class="form-1">

                <div class="form-title">
                    Customize registration form
                </div>

                <div class="checkbox-1 disabled" v-for="field in requiredFields">
                    <label>
                        <i></i>{{ field.displayName }}
                    </label>
                </div>

                <div class="checkbox-1" v-for="field in fields">
                    <label>
                        <input type="checkbox" name="{{ field.name }}" value="1" v-model="field['active']" />
                        <i></i>{{ field.displayName }}
                    </label>
                </div>

                <div class="form-row form-extra-field" v-for="field in extraFields">
                    <input type="text" name="extra[]" placeholder="Field name" v-model="field['displayName']" />
                    <i class="fa fa-close" @click="removeExtra($index)"></i>
                </div>

                <div class="form-row form-extra-button">
                    <button type="button" class="button-gray" @click="addExtra()">Add extra field</button>
                </div>

                <div class="form-buttons">
                    <button type="button" class="button-gray button-large" @click="showPreview()">Preview</button>
                    <button type="submit" class="button-green button-large" @click="submit()">Update</button>
                </div>

            </form>
        </div>
    </div>

    <modal v-ref:modal>
        <div class="modal-1">
            <div class="header">
                Registration form preview
            </div>
            <div class="body">
                <form class="form-1">
                    <div class="form-row" v-for="field in requiredFields">
                        <label>{{ field.displayName }}</label>
                        <input type="text" placeholder="{{ field.displayName }}" />
                    </div>
                    <div class="form-row" v-for="field in fields">
                        <label>{{ field.displayName }}</label>
                        <input type="text" placeholder="{{ field.displayName }}" />
                    </div>
                    <div class="form-row" v-for="field in extraFields">
                        <label>{{ field.displayName }}</label>
                        <input type="text" placeholder="{{ field.displayName }}" />
                    </div>
                </form>
            </div>
            <div class="footer">
                <button type="button" class="button-red" @click="closePreview()">Close</button>
            </div>
        </div>
    </modal>
</template>

<script>
export default {
    data: function () {
        return {
            requiredFields: {},
            fields: {},
            extraFields: [],
            errors: []
        };
    },

    created: function () {
        this.$http.get('/api/school/teacher/get-registration-form', function(data) {
            this.requiredFields = data.requiredFields;
            this.fields = data.fields;
            this.extraFields = data.extraFields;
        });
    },

    methods: {
        addExtra: function() {
            this.extraFields.push({name: '', displayName: '', active: true});
        },

        removeExtra: function(val) {
            this.extraFields.splice(val, 1);
        },

        submit: function() {
            this.$http.post('/api/school/teacher/update-registration-form', JSON.stringify({
                form: {
                    fields: this.fields,
                    extraFields: this.extraFields
                }
            }), function(response) {
                this.extraFields = response.extraFields;

                this.$dispatch('flash', 'success', 'Registration form updated!');
            });
        },

        showPreview: function() {
            this.$refs.modal.open();
        },

        closePreview: function() {
            this.$refs.modal.close();
        }
    }
}
</script>

<style lang="sass">
    #form-options {
        padding: 20px;

        .form-extra-field {
            display: flex;
            justify-content: flex-start;

            input {
                flex-basis: 50%;
            }
        }

        i.fa-close {
            font-size: 30px;
            color: #ac2925;
            height: 40px;
            line-height: 40px;
            vertical-align: middle;
            cursor: pointer;
            margin-left: 8px;
        }

        .form-extra-button {
            justify-content: flex-start;
            margin-right: 20px;
            font-size: 16px;
            font-weight: 400;
        }
    }
</style>