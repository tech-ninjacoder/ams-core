<template>
    <wizard-modal id="project-wizard-modal"
           size="large"
           v-model="showModal"
           :close-button="false"
           :loading="loading"
           :preloader="preloader">



        <app-breadcrumb            :pageTitle="$t('wizard')"
        />
            <app-form-wizard
                    :tabs="tabs"
            />
    </wizard-modal>
</template>

<script>
    import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
    import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
    import {localToUtc, formatDateForServer, formatUtcToLocal} from "../../../../common/Helper/Support/DateTimeHelper";
    import {cloneDeep} from 'lodash'
    import {errorMessageForArray} from '../../../../common/Helper/Support/FormHelper'
    import {PROJECTS} from "../../../Config/ApiUrl";

    export default {
        name: "formWizard",
        mixins: [FormHelperMixins, ModalMixin],

        data() {
            return {
                tabs: [
                    {
                        name: this.$t('basic_info'),
                        component: "form-wizard-basic-info"
                    },
                    {
                        name: this.$t('contact_info'),
                        component: "form-wizard-contact-info"
                    },
                    // {
                    //     name: this.$t('others'),
                    //     component: "form-wizard-others"
                    // },
                ],
            };
        }
    };
</script>
