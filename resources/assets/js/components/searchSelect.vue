<template>
    <div class="search-select" tabindex="0" @focus="handleMainFocused()">
        <select v-el:select name="{{ name }}" v-model="model">
            <option v-for="d in data" value="{{ d[value] }}"></option>
        </select>
        <div class="fake-select" @mousedown.prevent @click="handleFakeClick()">
            {{ selected[display] }}
            <div class="select-arrow">
                <i class="fa fa-sort-desc"></i>
            </div>
        </div>
        <div class="dropdown" v-if="showResults" @mousedown.prevent>
            <input
                    v-el:search-input
                    type="text"
                    placeholder="{{ placeholder }}"
                    @input="handleInput()"
                    @blur="handleFocusout()"
                    @keyUp.esc="handleEscape()"
                    @keyDown.enter.prevent="handleEnter()"
                    @keyDown.up="handleUp()"
                    @keyDown.down="handleDown()"
                    v-model="search"
            />
            <div class="results" v-el:results>
                <div
                        v-for="d in data | filterBy search | count | orderBy display"
                        @mousedown.prevent
                        @click="handleResultClick(d)"
                        :class="{'chosen': shouldApplyChosenClass($index)}"
                        data-value="{{ d[value] }}"
                        data-display="{{ d[display] }}"
                        @mouseover="handleResultMouseover($index)"
                >
                    {{ d[display] }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['data', 'name', 'value', 'model', 'display', 'placeholder'],

    data: function() {
        return {
            selected: this.initSelected(),
            search: '',
            showResults: false,
            chosenIndex: 0,
        };
    },

    filters: {
        count: function (arr) {
            this.$set('filterLength', arr.length);

            return arr;
        }
    },

    created: function() {
        if (!this.model) {
            this.model = this.selected[this.value];
        }
    },

    methods: {
        initSelected: function() {
            for (var i = 0; i < this.data.length; i++) {
                if (this.data[i][this.value] == this.model) {
                    return this.data[i];
                }
            }

            this.model = this.data[0][this.value];
            return this.data[0];
        },

        shouldApplyChosenClass: function(index) {
            return this.chosenIndex == index;
        },

        handleMainFocused: function () {
            if (this.showResults) {
                return;
            }

            this.showResults = true;

            Vue.nextTick(function() {
                this.$els.searchInput.focus();
            }.bind(this))
        },

        handleFakeClick: function() {
            if (this.showResults) {
                return;
            }

            this.showResults = true;

            Vue.nextTick(function() {
                this.$els.searchInput.focus();
            }.bind(this))
        },

        handleInput: function() {
            this.chosenIndex = 0;
        },

        handleFocusout: function() {
            this.showResults = false;
            this.search = '';
            this.chosenIndex = 0;
        },

        handleEscape: function() {
            this.showResults = false;
            this.search = '';
            this.chosenIndex = 0;
        },

        handleEnter: function() {
            this.selected[this.value] = $('.results .chosen:first').data('value');
            this.selected[this.display] = $('.results .chosen:first').data('display');
            this.model = this.selected[this.value];
            this.search = '';
            this.showResults = false;

            window.Vue.nextTick(function() {
                $(this.$els.select).trigger('change');
            }.bind(this));
        },

        handleUp: function() {
            if (!this.showResults) {
                return;
            }

            if (this.chosenIndex > 0) {
                this.chosenIndex--;
            }

            this.fixResultScroll();
        },

        handleDown: function() {
            if (!this.showResults) {
                return;
            }

            if (this.chosenIndex < this.filterLength - 1) {
                this.chosenIndex++;
            }

            this.fixResultScroll();
        },

        handleResultClick: function(d) {
            this.selected = d;
            this.model = d[this.value];
            this.search = '';
            this.showResults = false;
            this.chosenIndex = 0;

            window.Vue.nextTick(function() {
                $(this.$els.select).trigger('change');
            }.bind(this));
        },

        handleResultMouseover: function(index) {
            this.chosenIndex = index;
        },

        fixResultScroll: function() {
            if ((this.chosenIndex + 1) * 30 > this.$els.results.scrollTop + 150) {
                this.$els.results.scrollTop = (this.chosenIndex + 1) * 30 - 150;
            } else if ((this.chosenIndex + 1) * 30 <= this.$els.results.scrollTop) {
                this.$els.results.scrollTop = this.chosenIndex * 30;
            }
        }
    }
}
</script>