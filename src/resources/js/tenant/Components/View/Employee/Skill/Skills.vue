<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('skill')">
            <app-default-button
                :title="$fieldTitle('add', 'skill', true)"
                v-if="$can('create_skills')"
                @click="openModal()"
            />
        </app-page-top-section>

        <app-table
            id="skill-table"
            :options="options"
            @action="triggerActions"
        />

        <app-skill-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('skill-table')"
            @cancelled="cancelled"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
    import SkillMixin from "../../../Mixins/SkillMixin";
    import {SKILL} from "../../../../Config/ApiUrl";
    import {mapState} from "vuex";

    export default {

        name: "AppSkill",
        mixins: [HelperMixin, SkillMixin],
        data() {
            return {
                isModalActive: false,
                selectedUrl: '',
            }
        },

        methods: {
            triggerActions(row, action, active) {
                if (action.title === this.$t('edit')) {
                    this.selectedUrl = `${SKILL}/${row.id}`;
                    this.isModalActive = true;
                } else {
                    this.getAction(row, action, active)
                }
            },

            openModal() {
                this.isModalActive = true;
                this.selectedUrl = ''
            },
        },
    }
</script>
