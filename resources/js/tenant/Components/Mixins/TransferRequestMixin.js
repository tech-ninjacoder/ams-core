import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {TRANSFER_REQUEST} from "../../Config/ApiUrl";

export default {
    mixins: [DatatableHelperMixin],
    data() {

        return {
            options: {
                name: this.$t('tenant_groups'),
                url: TRANSFER_REQUEST,
                showHeader: true,
                showCount: true,
                columns: [
                    {
                        title: this.$t('title'),
                        type: 'text',
                        key: 'title',
                        isVisible: true,
                    },
                    {
                        title: this.$t('status'),
                        type: 'text',
                        key: 'status',
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
                        title: this.$t('no_of_comments'),
                        type: 'text',
                        key: 'comments_count',
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
                        modifier: () => this.$can('view_designations')
                    },
                    {
                        title: this.$t('status'),
                        icon: 'trello',
                        type: 'modal',
                        modifier: () => this.$can('view_designations')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash-2',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: TRANSFER_REQUEST,
                        name: 'delete',
                        modifier:( row) => this.$can('delete_designations') && !parseInt(row.is_default)
                    }
                ],
            },
        }
    }
}
