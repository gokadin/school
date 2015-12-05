<script>
export default {
    data: function () {
        return {
            regularFields: {},
            extraFields: {},
            defaultFields: {}
        };
    },

    created: function () {
        this.$http.get('/api/school/teacher/get-registration-form', function(data) {
            this.regularFields = data.regularFields;
            this.extraFields = data.extraFields;
            this.defaultFields = data.defaultFields;
        });
    },

    methods: {
        addExtra: function() {

        },

        removeExtra: function(val) {

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