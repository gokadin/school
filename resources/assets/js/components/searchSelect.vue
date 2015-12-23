<template>
    <div class="search-select">
        <select name="{{ name }}" v-model="selected">
            <option v-for="d in data" value="{{ d[value] }}">{{ d[display] }}</option>
        </select>
        <input
                type="text"
                placeholder="{{ placeholder }}"
                @click="handleInputClick()"
                @input="handleInput()"
                @keyUp.esc="handleEscape()"
                @keyup.13.prevent="handleEnter()"
                @keyUp.up="handleUp()"
                @keyUp.down="handleDown()"
                v-model="search"
        />
        <div class="results" v-if="showResults">
            <div
                v-for="d in data | filterBy search | orderBy display"
                @mousedown.prevent
                @click="handleResultClick(d, $index)"
                :class="{'chosen': shouldApplyChosenClass($index)}"
                data-value="{{ d[value] }}"
                data-display="{{ d[display] }}"
                @mouseover="handleResultMouseover($index)"
            >
                {{ d[display] }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['data', 'name', 'value', 'display', 'placeholder'],

    data: function() {
        return {
            selected: this.data[0][this.value],
            search: '',
            showResults: true,
            chosenIndex: 0
        };
    },

    methods: {
        shouldApplyChosenClass: function(index) {
            return this.chosenIndex == index;
        },

        handleInputClick: function() {
            this.showResults = true;
        },

        handleInput: function() {
            this.chosenIndex = 0;
            this.showResults = true;
        },

        handleEscape: function() {
            this.showResults = false;
        },

        handleEnter: function() {
            this.selected = $('.results .chosen:first').data('value');
            this.search = $('.results .chosen:first').data('display');
            this.showResults = false;
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

            if (this.chosenIndex < this.data.length - 1) {
                this.chosenIndex++;
            }

            this.fixResultScroll();
        },

        handleResultClick: function(d, index) {
            this.selected = d[this.value];
            this.search = d[this.display];
            this.showResults = false;
            this.chosenIndex = index;
        },

        handleResultMouseover: function(index) {
            this.chosenIndex = index;
        },

        fixResultScroll: function() {
            if ((this.chosenIndex + 1) * 30 > $('.results').scrollTop() + 150) {
                $('.results').scrollTop((this.chosenIndex + 1) * 30 - 150);
            } else if ((this.chosenIndex + 1) * 30 <= $('.results').scrollTop()) {
                $('.results').scrollTop(this.chosenIndex * 30);
            }
        }
    }
}
</script>