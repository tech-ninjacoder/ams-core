import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {CONTRACTOR,} from "../../Config/ApiUrl";

export default {
    mixins: [DatatableHelperMixin],
    data() {

        return {
            options: {
                name: this.$t('tenant_groups'),
                url: CONTRACTOR,
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
                        title: this.$t('phone_number'),
                        type: 'text',
                        key: 'phone_number',
                        isVisible: true,
                    },
                    {
                        title: this.$t('note'),
                        type: 'custom-html',
                        key: 'note',
                        isVisible: true,
                        modifier: (description) => {
                            return  description ? description : '-';
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
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
                        modifier: () => this.$can('update_contractors')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash-2',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: CONTRACTOR,
                        name: 'delete',
                        modifier:( row) => this.$can('delete_contractors') && !parseInt(row.is_default)
                    }
                ],
            },
        }
    }
}
