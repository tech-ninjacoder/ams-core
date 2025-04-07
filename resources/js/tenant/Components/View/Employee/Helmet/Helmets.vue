<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('helmet')">
            <app-default-button
                :title="$fieldTitle('add', 'helmet', true)"
                v-if="$can('create_designations')"
                @click="openModal()"
            />
            <app-default-button
                    :title="$fieldTitle('sync', 'helmet', true)"
                    v-if="$can('create_designations')"
                    @click="sync()"
            />


        </app-page-top-section>

        <app-table
            id="helmet-table"
            :options="options"
            @action="triggerActions"
        />

        <app-helmet-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('helmet-table')"
            @cancelled="cancelled"
        />
        <app-release-confirmation-modal
                v-if="ReleaseconfirmationModalActive"
                icon="trash-2"
                modal-id="app-release-confirmation-modal"
                @releaseconfirmed="releaseconfirmed('helmet-table')"
                @cancelled="cancelled"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
    import HelmetMixin from "../../../Mixins/HelmetMixin";
    import {HELMET, HELMET_SYNC} from "../../../../Config/ApiUrl";
    import {mapState} from "vuex";
    import coreLibrary from "../../../../../core/helpers/CoreLibrary";




    export default {

        name: "AppHelmet",
        extends: coreLibrary,
        mixins: [HelperMixin, HelmetMixin],
        data() {
            return {
                isModalActive: false,
                formData: {},
                selectedUrl: '',
                HELMET_SYNC,
            }
        },

        methods: {
            triggerActions(row, action, active) {
                if (action.title === this.$t('edit')) {
                    this.selectedUrl = `${HELMET}/${row.id}`;
                    this.isModalActive = true;
                } else {
                    this.getAction(row, action, active)
                }
            },

            openModal() {
                this.isModalActive = true;
                this.selectedUrl = ''
            },
            sync(){
                // const sdata = axios.get(HELMET_SYNC);
                // data = sdata;
                // this.toastAndReload(data.message, 'helmet-table');
                let axioscall = "";
                axioscall = this.axiosGet(HELMET_SYNC)
                    .then((response) => {

                            this.afterSuccessFromGetEditData(response);
                        console.log('afterSuccessFromGetEditData')


                    }).catch(({response}) => {
                        console.log('response')


                        this.afterErrorFromGetEditData(response);
                })
                    .then((response) => {
                    console.log('then')
                    console.log(response)



                    //trigger after success
                    //     this.afterSuccess(response);

                })

            },
            // afterSuccess({response}) {
            //     this.formData = {};
            //     this.toastAndReload(response.message, 'helmet-table');
            //     console.log('afterSuccess')
            //
            // },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
                this.toastAndReload(response.data.message, 'helmet-table');

                console.log('hoppa')
                console.log('aa'+response.message)


            },
        },
    }
</script>
