import {FilterCloseMixin} from "./FilterCloseMixin";

export const FilterMixin = {
    mixins: [FilterCloseMixin],
    props: {
        filterKey: String,
        tableId: String,
    },
    data() {
        return {
            isApply: false,
            value: null,
            filterId: null,
        }
    },
    created() {
        this.filterId = `${this.filterKey}-${this.tableId}`;
    },
    methods: {
        returnValue(value) {
            this.$emit('get-value', {'key': this.filterKey, 'value': value})
            // console.log({'key': this.filterKey, 'value': value})
            // this.$delete(this.$filters_front,this.filterKey);
            // for (let i = 0; i < this.$filters_front.length; i++) {
            //     for (let j = i + 1; j < this.$filters_front.length; j++) {
            //         if (this.$filters_front[i].key === this.$filters_front[j].key) {
            //             this.$filters_front.splice(j, 1);
            //             j--;
            //             console.log('spliced')
            //         }
            //     }
            // }

            for (let i = 0; i < this.$filters_front.length; i++) {
                    if (this.$filters_front[i].key === this.filterKey) {
                        this.$filters_front.splice(i, 1);
                        i++;
                        // console.log('spliced')
                    }

            }
            console.log(this.$filters_front)
            this.$filters_front.push({'key': this.filterKey, 'value': value});

        },
    }
};
