import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {SUBDIVISION,} from "../../Config/ApiUrl";

export default {
    mixins: [DatatableHelperMixin],
    data() {

        return {
            options: {
                name: this.$t('tenant_groups'),
                url: SUBDIVISION,
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
                        modifier: () => this.$can('update_subdivisions')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash-2',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: SUBDIVISION,
                        name: 'delete',
                        modifier:( row) => this.$can('delete_subdivisions') && !parseInt(row.is_default)
                    }
                ],
            },
        }
    }
}
