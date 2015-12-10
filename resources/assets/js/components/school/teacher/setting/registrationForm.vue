<template>
    <div class="panel-1">
        <div id="form-options">
            <form v-on:submit.prevent class="form-1">

                <div class="form-title">
                    Customize registration form
                </div>

                <div class="checkbox-1 disabled" v-for="field in defaultFields">
                    <label>
                        <i></i>{{ field.displayName }}
                    </label>
                </div>

                <div class="checkbox-1" v-for="field in regularFields">
                    <label>
                        <input type="checkbox" name="@{{ $key }}" value="1" v-model="regularFields[$key]['value']" />
                        <i></i>{{ field.displayName }}
                    </label>
                </div>

                <div class="form-row" v-for="fieldId in extraFields">
                    <input type="text" name="extra[]" placeholder="Field name" v-model="extraFields[$index]['displayName']" />
                    <i class="fa fa-close" @click="removeExtra($index)"></i>
                </div>

                <div class="form-row">
                    <button type="button" class="button-gray" @click="addExtra()">Add extra field</button>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="button-green button-large" @click="submit()">Update</button>
                </div>

            </form>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            originalData: {},
            regularFields: {},
            extraFields: [],
            defaultFields: {}
        };
    },

    created: function () {
        this.$http.get('/api/school/teacher/get-registration-form', function(data) {
            this.originalData = data;
            this.regularFields = data.regularFields;
            this.defaultFields = data.defaultFields;
            this.extraFields = data.extraFields;
        });
    },

    methods: {
        addExtra: function() {
            this.extraFields.push({newExtra: ''});
        },

        removeExtra: function(val) {
            this.extraFields.splice(val, 1);
        },

        submit: function() {
            this.$http.post('/api/school/teacher/update-registration-form', {
                regularFields: this.regularFields,
                extraFields: this.extraFields
            }, function(response) {

            });
        }
    }
}
</script>

<style lang="sass">
    #form-options {
        padding: 20px;

        i.fa-close {
            font-size: 30px;
            color: #ac2925;
            height: 40px;
            line-height: 40px;
            vertical-align: middle;
            cursor: pointer;
            margin-left: 8px;
        }
    }
</style>