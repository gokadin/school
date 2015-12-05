<script>
    export default {
        data: function() {
            return {
                newStudents: {},
                mainFilter: '',
                newStudentsOpen: true
            };
        },

        created: function() {
            this.$http.get('/api/school/teacher-new-students', function(data) {
                this.newStudents = data;
            });
        }
    };
</script>

<style lang="sass">
    .expired span,
    .pending span {
        padding: 3px;
        color: white;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }

    .expired span {
        background-color: #ac2925;
    }

    .pending span {
        background-color: #f2c200;
    }
</style>