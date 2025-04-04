import {axiosDelete} from "../../Helper/AxiosHelper";
import {axiosRelease} from "../../Helper/AxiosHelper";
import TriggerActionMixin from "./TriggerActionMixin";
import HelperMixin from "./HelperMixin";

export default {
    mixins: [TriggerActionMixin, HelperMixin],
    data() {
        return {
            confirmationModalActive: false,
            ReleaseconfirmationModalActive: false,
            delete_url: '',
            selected_url: ''
        }
    },

    methods: {
        confirmed(id = null, second = null) {
            return axiosDelete(this.delete_url).then(response => {
                this.toastAndReload(response.data.message, id)
                if (second) {
                    this.$hub.$emit(`reload-${second}`)
                }
                this.confirmationModalActive = false;
            }).catch((error) => {
                if (error.response)
                    this.toastException(error.response.data)
            });
        },
        releaseconfirmed(id = null, second = null) {
            console.log('aaaaa');

            return axiosRelease(this.delete_url).then(response => {
                this.toastAndReload(response.data.message, id)
                if (second) {
                    this.$hub.$emit(`reload-${second}`)
                }
                this.ReleaseconfirmationModalActive = false;
            }).catch((error) => {
                if (error.response)
                    this.toastException(error.response.data)
            });
        },
        releaseparentconfirmed(id = null, second = null) {
            console.log('eeee');
            return axiosRelease(this.selectedUrl).then(response => {
                this.toastAndReload(response.data.message, id)
                if (second) {
                    this.$hub.$emit(`reload-${second}`)
                }
                this.ReleaseconfirmationModalActive = false;
            }).catch((error) => {
                if (error.response)
                    this.toastException(error.response.data)
            });
        },
        cancelled() {
            this.confirmationModalActive = false;
        },
    },

    mounted() {
        $('#app-confirmation-modal').on('hidden.bs.modal', () => {
            this.confirmationModalActive = false
        }),
            $('#app-release-confirmation-modal').on('hidden.bs.modal', () => {
                this.ReleaseconfirmationModalActive = false
            })
    },

    watch: {
        confirmationModalActive: function (flag) {
            if (flag) {
                setTimeout(() => $('#app-confirmation-modal').modal('show'))
            } else {
                this.delete_url = ''
            }
        },
        ReleaseconfirmationModalActive: function (flag) {
            if (flag) {
                setTimeout(() => $('#app-release-confirmation-modal').modal('show'))
            } else {
                this.delete_url = ''
            }
        }
    }
}
