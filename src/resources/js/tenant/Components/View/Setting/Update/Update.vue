<template>
    <div v-if="!loading">
        <app-note
            :class="'mb-primary'"
            :title="$fieldTitle('update')"
            content-type="html"
            :notes="Object.keys(no_update_message).length ?
             `${no_update_message.message_1} ${no_update_message.message_2 ? '<br>' : ''} ${no_update_message.message_2}`
             : $t('update_warning')"
        />
        <div class="d-flex align-items-center justify-content-between mb-3" v-for="update in updates.result">
            {{ update.version }}

            <div class="d-flex align-items-center justify-content-end">
                <a
                    class="btn btn-outline-primary btn-sm rounded-pill"
                    href="https://pme.vt-lb.com">
                    {{ $t('change_log') }}
                </a>
                <div class="btn-group btn-group-action d-inline-block ml-4">
                    <button type="button"
                            class="btn"
                            data-toggle="tooltip"
                            :title="$t('update')"
                            data-placement="top"
                            @click="updateApp(update.version)">
                        <app-icon name="download"/>
                    </button>
                </div>
            </div>
        </div>
        <app-update-confirmation-modal v-if="confirmationModalActive"
                                       modal-id="app-confirmation-modal"
                                       @confirmed="confirmed()"
                                       @cancelled="cancelled"
                                       :message="$t('this_will_update_entire_app')"/>
    </div>
    <app-overlay-loader v-else/>
</template>

<script>
    import {axiosPost, axiosGet} from "../../../../../common/Helper/AxiosHelper";
    import {APP_UPDATE} from "../../../../Config/ApiUrl";

    export default {
        name: "Update",
        data() {
            return {
                updates: {},
                loading: true,
                confirmationModalActive: false,
                selectedVersion: '',
                no_update_message: {}
            }
        },
        mounted() {
            setTimeout(function () {
                $('[data-toggle="tooltip"]').tooltip();
            }, 6000);
        },
        methods: {
            updateApp(version) {
                this.selectedVersion = version;
                this.confirmationModalActive = true;
            },
            confirmed() {
                this.loading = true;
                axiosPost(`app/updates/install/${this.selectedVersion}`, {}).then(({data}) => {
                    this.$toastr.s('', data.message);
                    this.getUpdates(false);
                    this.confirmationModalActive = false;
                }).catch(({response}) => {
                    this.$toastr.e('', response.data.message);
                }).finally(() => {
                    this.loader = false;
                })
            },
            cancelled() {
                this.confirmationModalActive = false;
                this.selectedVersion = '';
            },
            getUpdates(showError = true) {
                axiosGet(APP_UPDATE).then(response => {
                    this.updates = response.data;
                }).catch(error => {
                  try {
                    this.no_update_message = JSON.parse(error.response.data.message)
                  }catch (e) {
                    if (showError) {
                      this.no_update_message.message_1 = error.response.data.message;
                      this.no_update_message.message_2 = '';
                    }
                  }
                  this.updates = {result: []};
                }).finally(() => {
                    this.loading = false;
                    location.reload();
                })
            }
        },
        created() {
            this.getUpdates();
        }
    }
</script>
