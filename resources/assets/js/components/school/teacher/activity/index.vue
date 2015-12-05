<script>
    export default {
        data: function() {
            return {
                uri: '/api/school/teacher-activities',
                columns: [
                    { name: 'name', display: 'Name', sortable: true, searchable: true },
                    { name: 'rate', display: 'Rate', sortable: true, searchable: true },
                    { name: 'period', display: 'Period', sortable: true, searchable: true },
                    { name: 'students', display: 'Students',sortable: true, searchable: false }
                ],
                actions: 'blabla'
            };
        }
    };
</script>