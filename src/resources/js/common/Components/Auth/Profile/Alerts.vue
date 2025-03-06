<template>
    <div class="content">
        <app-table id="employee-alerts-table" :options="options"/>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../core/helpers/CoreLibrary.js";
    import {formatDateToLocal} from "../../../Helper/Support/DateTimeHelper";
    import {ucFirst} from '../../../Helper/Support/TextHelper'

    export default {
        name: "Activity",
        extends: CoreLibrary,
        components: {
            'app-alert-details': require('./Component/AlertDetails').default
        },
        props: {
            props: {

            }
        },
        data(){
            return{
                options: {
                    url: this.props?.id ? `admin/user/alerts/${this.props.id}` :'admin/user/alert-log',
                    showHeader: true,
                    tableShadow: false,
                    showManageColumn: false,
                    tablePaddingClass: 'px-0 py-primary',
                    columns: [
                        {
                            title: this.$t('type'),
                            type: 'custom-html',
                            key: 'type',
                            isVisible: true,
                            modifier: (type) => {
                                let user = '';
                                let badge = ''
                                // user = value.full_name ? '<a href="/employees/'+users.user_id+'/profile"><span class="badge badge-pill badge-primary text-capitalize">'+value.full_name+'</span></a>' : '<span class="badge badge-pill badge-danger text-capitalize">-</span>';
                                badge ='<span class="badge badge-pill badge-danger text-capitalize">'+type+'</span>'
                                return badge;
                            }
                        },
                        {
                            title: this.$t('note'),
                            type: 'text',
                            key: 'note',
                        },
                        // {
                        //     title: this.$t('name'),
                        //     type: 'custom-html',
                        //     key: 'subject',
                        //     modifier: (subject, alert) => {
                        //         if (subject)
                        //             return (ucFirst(this.$t(subject.name)) || subject.full_name) ||
                        //                 subject.subject;
                        //         let attributes = this.$optional(alert, 'properties', 'attributes');
                        //         let old = this.$optional(alert, 'properties', 'old');
                        //         if (attributes && Object.keys(attributes).length) {
                        //             return (attributes.name || attributes.full_name) || attributes.subject;
                        //         }else if(old && Object.keys(old).length){
                        //             return (old.name || old.full_name) || old.subject;
                        //         }
                        //     }
                        // },
                        // {
                        //     title: this.$t('date'),
                        //     type: 'text',
                        //     key: 'date',
                        // },
                        {
                            title: this.$fieldTitle('time'),
                            type: 'custom-html',
                            key: 'created_at',
                            modifier: created_at => formatDateToLocal(created_at, true)
                        }
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
