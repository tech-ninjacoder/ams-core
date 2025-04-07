<template>

    <div class="content">
        <app-table id="activity-table" :options="options"/>
    </div>
</template>
<script>
    import CoreLibrary from "../../../../core/helpers/CoreLibrary.js";
    import {formatDateToLocal} from "../../../../common/Helper/Support/DateTimeHelper";
    import {ucFirst} from '../../../../common/Helper/Support/TextHelper'

    export default {
        name: "app-project-active-employees",
        // extends: CoreLibrary,
        components: {
            'app-project-details': require('./components/ActivityDetails').default
        },
        props: {
            props: {

            }
        },
        data(){
            return{
                options: {
                    url: this.props?.id ? `app/employees/?projects=${this.props.id}` :'admin/project/activity-log',
                    // url: this.props?.id ? `app/projects/employees/${this.props.id}` :'admin/project/activity-log',

                    showHeader: true,
                    tableShadow: true,
                    showManageColumn: true,
                    tablePaddingClass: 'px-0 py-primary',
                    columns: [
                        {
                            title: this.$t('id'),
                            type: 'text',
                            key: 'id',
                        },
                        {
                            title: this.$t('employee_id'),
                            type: 'object',
                            key: 'profile',
                            isVisible: true,
                            modifier: (value, row) => {
                                return value ? value.employee_id : '-';
                            }
                        },
                        {
                            title: this.$t('name'),
                            type: 'text',
                            key: 'full_name',

                        },
                        // {
                        //     title: this.$t('name'),
                        //     type: 'custom-html',
                        //     key: 'subject',
                        //     modifier: (subject, activity) => {
                        //         if (subject)
                        //             return (ucFirst(this.$t(subject.name)) || subject.full_name) ||
                        //                 subject.subject;
                        //         let attributes = this.$optional(activity, 'properties', 'attributes');
                        //         let old = this.$optional(activity, 'properties', 'old');
                        //         if (attributes && Object.keys(attributes).length) {
                        //             return (attributes.name || attributes.full_name) || attributes.subject;
                        //         }else if(old && Object.keys(old).length){
                        //             return (old.name || old.full_name) || old.subject;
                        //         }
                        //     }
                        // },
                        {
                            title: this.$t('start_date'),
                            type: 'custom-html',
                            key: 'projects',
                            modifier: (projects) => {
                                let start_date = null;

                                projects.forEach((value,index) => {
                                    start_date = value.pivot.start_date;
                                    // console.log(value.name);
                                    // console.log(index);
                                });
                                return formatDateToLocal(start_date,true);

                            }
                        },
                        // {
                        //     title: this.$fieldTitle('time'),
                        //     type: 'custom-html',
                        //     key: 'created_at',
                        //     modifier: created_at => formatDateToLocal(created_at, true)
                        // }
                        {
                            title: this.$t('skill'),
                            type: 'object',
                            key: 'skills',
                            isVisible: true,
                            modifier: (skills) => {
                                let skillName = '';
                                skills.forEach((element, index) => {
                                    skillName += index > 0 ? ', ' + element.name : element.name;
                                });
                                skillName = skillName ? skillName : '-';
                                return skillName;
                            }
                        },
                        {
                            title: this.$t('provider'),
                            type: 'object',
                            key: 'provider',
                            isVisible: true,
                            modifier: (provider, row) => {
                                return provider ? provider.name : '-';
                            }
                        },
                    ],
                    filters: [

                    ],
                    paginationType: "loadMore",
                    responsive: true,
                    rowLimit: 10,
                    showAction: false,
                    orderBy: 'desc',
                }
            }
        }
    }
</script>
