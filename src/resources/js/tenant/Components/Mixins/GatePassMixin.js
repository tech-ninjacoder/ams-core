import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {GATE_PASSE,RELEASE_GATE_PASS} from "../../Config/ApiUrl";

export default {
    mixins: [DatatableHelperMixin],
    data() {

        return {
            options: {
                name: this.$t('tenant_groups'),
                url: GATE_PASSE,
                showHeader: true,
                showCount: true,
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('valid_from'),
                        type: 'text',
                        key: 'valid_from',
                        isVisible: true,
                    },
                    {
                        title: this.$t('valid_to'),
                        type: 'text',
                        key: 'valid_to',
                        isVisible: true,
                    },
                    {
                        title: this.$t('description'),
                        type: 'custom-html',
                        key: 'description',
                        isVisible: true,
                        modifier: (description) => {
                            return  description ? description : '-';
                        }
                    },
                    {
                        title: this.$t('no_of_user'),
                        type: 'text',
                        key: 'users_count',
                        isVisible: true,
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    },
                ],
                filters: [
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('Valid'),
                        type: "radio",
                        key: "valid",
                        option: [
                            {id: '1', value: this.$t('yes')},
                        ],
                        permission: !!this.$can('view_employees')
                        ,

                        "header": {
                            "title": this.$t('get_valid_gatepassess')
                        },
                    },

                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        modifier: () => this.$can('update_designations')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash-2',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: GATE_PASSE,
                        name: 'delete',
                        modifier:( row) => this.$can('delete_designations') && !parseInt(row.is_default)
                    },
                    {
                        title: this.$t('release'),
                        icon: 'unlock',
                        type: 'modal',
                        component: 'app-release-confirmation-modal',
                        modalId: 'app-release-confirmation-modal',
                        url: RELEASE_GATE_PASS,
                        name: 'release',
                        modifier:( row) => this.$can('delete_designations') && !parseInt(row.is_default)
                    },
                ],
            },
        }
    }
}
