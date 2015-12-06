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