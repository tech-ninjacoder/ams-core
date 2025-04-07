import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {ALERTS,RELEASE_GATE_PASS} from "../../Config/ApiUrl";

export default {
    mixins: [DatatableHelperMixin],
    data() {

        return {
            options: {
                name: this.$t('tenant_groups'),
                url: ALERTS,
                showHeader: true,
                showCount: true,
                columns: [
                    // {
                    //     title: this.$t('type'),
                    //     type: 'text',
                    //     key: 'type',
                    //     isVisible: true,
                    // },
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
                        title: this.$t('date'),
                        type: 'text',
                        key: 'date',
                        isVisible: true,
                    },
                    {
                        title: this.$t('user'),
                        type: 'custom-html',
                        key: 'users',
                        isVisible: true,
                        modifier: (value ,users) => {
                            let user = '';
                            user = value.full_name ? '<a href="/employees/'+users.user_id+'/profile"><span class="badge badge-pill badge-primary text-capitalize">'+value.full_name+'</span></a>' : '<span class="badge badge-pill badge-danger text-capitalize">-</span>';
                            return user;
                        }
                    },
                    {
                        title: this.$t('note'),
                        type: 'text',
                        key: 'note',
                        isVisible: true,
                    }
                ],
                filters: [
                    // {
                    //     title: this.$t('today'),
                    //     type: "date",
                    //     key: "date_in",
                    //     initValue: new Date(), // not required
                    // },
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('type'),
                        type: "drop-down-filter",
                        key: "type",
                        option: [
                            {id: 'custom', name: this.$t('custom')},
                            {id: 'battery', name: this.$t('battery')},
                            {id: 'Wrong_site', name: this.$t('wrong site')},
                            {id: 'offline_duration', name: this.$t('offline duration')},
                            {id: 'sos', name: this.$t('sos')},

                            //Wrong_site
                        ],
                        listValueField: 'name'
                    },

                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "default",
                actions: [

                ],
            },
        }
    }
}
