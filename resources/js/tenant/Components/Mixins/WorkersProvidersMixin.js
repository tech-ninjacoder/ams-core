import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {WORKERS_PROVIDERS} from "../../Config/ApiUrl";
import {formatDateToLocal} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        return {
            options: {
                name: this.$t('workers_provider'),
                url: WORKERS_PROVIDERS,
                showHeader: true,
                showCount: true,
                showClearFilter: true,
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('description'),
                        type: 'custom-html',
                        key: 'description',
                        isVisible: true,
                        modifier: (description) => {
                            return description ? description : '-';
                        }
                    },
                    {
                        title: this.$t('created'),
                        type: 'custom-html',
                        key: 'created_at',
                        isVisible: true,
                        modifier: (value) => {
                            return formatDateToLocal(value);
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
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
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('edit'),
                        type: 'modal',
                        component: 'app-workers-providers-modal',
                        modalId: 'workers-providers-modal',
                        url: WORKERS_PROVIDERS,
                        name: 'edit',
                        modifier: row => this.$can('update_departments')
                    },
                ],
            }
        }
    }
}
