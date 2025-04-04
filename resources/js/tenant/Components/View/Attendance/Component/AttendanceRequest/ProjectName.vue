<template>
    <div class="d-flex align-items-center">
        <span class="mr-1">{{ projectName }}</span>
    </div>

</template>

<script>
    import {dateTimeFormat} from '../../../../../../common/Helper/Support/DateTimeHelper'

    export default {
        name:'ProjectName',
        props: {
            details: {
                type: Object,
                default: function () {
                    return {};
                }
            },
            comment: {
                type: Object,
                default: function () {
                    return {};
                }
            },
            project:{
                type: Object,
                default: function () {
                    return {};
                }
            },
            type: {
                type: String,
                default: 'punch-out'
            }
        },
        computed: {
            projectName(){
              const name = {'name' : 'name' };
                if (this.project[name]) {
                    return this.project[name]
                }
                return this.$t('not_yet_project')

            },
            inTime(){
                const field = {'punch-in': 'in_time', 'punch-out': 'out_time'}[this.type];
                if (this.details[field]) {
                    return dateTimeFormat(this.details[field])
                }
                return this.$t('not_yet')
            },
            user() {
                return window.user;
            },
            permission() {
                return false;
                //return this.$can('update_attendance_notes') && Number(this.user.id ) === Number(this.comment.user_id);
            }
        },
    }
</script>
