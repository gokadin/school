<template>
    <div class="tabs tabs-1">
        <div class="header">
            <div
                    v-for="tab in tabs"
                    :class="{ 'tab': true, 'active': tab.name == selected }"
                    @click="selectTab(tab)"
            >
                {{ tab.display }}
            </div>
        </div>
        <div class="body" v-for="tab in tabs" v-show="tab.name == selected">
            <slot :name="tab.name"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: ['tabs', 'selected'],

    data: function() {
        return {

        };
    },

    methods: {
        selectTab: function(tab) {
            this.selected = tab.name;
        }
    }
}
</script>