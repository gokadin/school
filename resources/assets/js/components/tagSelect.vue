<template>
    <div class="tag-select">
        <ul>
            <li class="tag"
                v-for="tag in tags"
                @mousedown.prevent
            >
                {{ tag[display] }}
                <i class="fa fa-close" @click="removeTag(tag)"></i>
            </li>
            <li class="search-tag">
                <input type="text"
                       v-model="searchValue"
                       debounce="300"
                       autocomplete="off"
                       maxlength="50"
                       :placeholder="placeholder"
                       v-el:input
                       @click="showResults()"
                       @focus="showResults()"
                       @blur="hideResults()"
                       @keyDown.enter.prevent="handleEnter()"
                       @keyUp.esc="handleEscape()"
                       @keyDown.up="handleUp()"
                       @keyDown.down="handleDown()"
                       @keyUp.8="handleBackspace()"
                />
            </li>
        </ul>
        <div class="dropdown" v-show="show" v-el:dropdown @mousedown.prevent>
            <div class="results">
                <div class="result"
                     v-for="data in dataSet"
                     @click="addTag(data)"
                     @mouseover="handleResultMouseover($index)"
                     :class="{'selected': shouldApplySelectedClass($index)}"
                >
                    {{ data[display] }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['name', 'placeholder', 'model', 'uri', 'method', 'value', 'display', 'searchkey'],

    data: function() {
        return {
            searchValue: '',
            dataSet: [],
            show: true,
            tags: [],
            currentSelection: 0
        };
    },

    watch: {
        'searchValue': function(val, oldVal) {
            this.showResults();
            this.currentSelection = 0;

            if (oldVal == val) {
                return;
            }

            val = val.trim();

            if (val == '') {
                this.dataSet = [];

                return;
            }

            this.fetchData(val);
        },

        'tags': function(val) {
            var idList = [];
            for (var i = 0; i < val.length; i++) {
                idList.push(val[i][this.value]);
            }

            this.model = idList;
        }
    },

    methods: {
        fetchData: function(val) {
            switch(this.method) {
                case 'post':
                    return this.fetchPostData(val);
            }
        },

        fetchPostData: function(val) {
            var data = {};
            data[this.searchkey] = val;

            this.$http.post(this.uri, data, function(response) {
                this.dataSet = response.results;
            });
        },

        addTag: function(data) {
            this.tags.push(data);

            this.searchValue = '';
            this.$els.input.focus();
        },

        removeTag: function(tag) {
            this.tags.$remove(tag);

            this.$els.input.focus();
        },

        removeLastTag: function() {
            this.removeTag(this.tags[this.tags.length - 1]);
        },

        showResults: function() {
            if (this.show) {
                return;
            }

            this.show = true;
        },

        hideResults: function() {
            if (!this.show) {
                return;
            }

            this.show = false;
        },

        handleEnter: function() {
            if (this.dataSet.length <= this.currentSelection) {
                return;
            }

            this.addTag(this.dataSet[this.currentSelection]);
        },

        handleEscape: function() {
            this.hideResults();
        },

        handleUp: function() {
            if (!this.show) {
                return;
            }

            if (this.currentSelection > 0) {
                this.currentSelection--;
            }

            this.fixResultScroll();
        },

        handleDown: function() {
            if (!this.show) {
                return;
            }

            if (this.currentSelection < this.dataSet.length - 1) {
                this.currentSelection++;
            }

            this.fixResultScroll();
        },

        handleBackspace: function() {
            if (this.searchValue != '') {
                return;
            }

            this.removeLastTag();
        },

        handleResultMouseover: function(index) {
            this.currentSelection = index;
        },

        shouldApplySelectedClass: function(index) {
            return this.currentSelection == index;
        },

        fixResultScroll: function() {
            if ((this.currentSelection + 1) * 30 > this.$els.dropdown.scrollTop + 210) {
                this.$els.dropdown.scrollTop = (this.currentSelection + 1) * 30 - 210;
            } else if ((this.currentSelection + 1) * 30 <= this.$els.dropdown.scrollTop) {
                this.$els.dropdown.scrollTop = this.currentSelection * 30;
            }
        }
    }
}
</script>