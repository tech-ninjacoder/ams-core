<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('projects')">
            <app-default-button
                :title="$fieldTitle('add', 'project', true)"
                v-if="$can('create_projects')"
                @click="openModal()"
            />
<!--            <app-default-button-->
<!--                    :title="$t('wizard')"-->
<!--                    v-if="$can('create_working_shifts')"-->
<!--                    @click="openWizardModal()"-->
<!--            />-->
        </app-page-top-section>

        <app-table
            id="project-table"
            :options="options"
            @action="triggerActions"
        />

        <app-project-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-wizard-project-modal
                v-if="isWizardModalActive"
                v-model="isWizardModalActive"
                @close="isWizardModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('project-table')"
            @cancelled="cancelled"
            icon="trash-2"
            sub-title=""
            :message="message"
            modal-class="danger"
        />
<!--        <app-project-release-confirmation-modal-->
<!--            v-if="ReleaseconfirmationModalActive"-->
<!--            modal-id="app-confirmation-modal"-->
<!--            @releaseconfirmed="releaseparentconfirmed('project-table')"-->
<!--            @cancelled="cancelled"-->
<!--            icon="trash-2"-->
<!--            sub-title=""-->
<!--            :message="message"-->
<!--            modal-class="danger"-->
<!--        />-->

        <app-release-confirmation-modal
                v-if="ReleaseconfirmationModalActive"
                icon="trash-2"
                modal-id="app-project-release-confirmation-modal"
                @releaseconfirmed="releaseconfirmed('project-table')"
                @cancelled="cancelled"
        />

        <app-employee-to-project
            v-if="isAddEmployeeModalActive"
            v-model="isAddEmployeeModalActive"
            :id="projectId"
        />
        <app-manager-to-project
                v-if="isAddManagerModalActive"
                v-model="isAddManagerModalActive"
                :id="projectId"
        />
        <app-working-shift-to-project
                v-if="isAddWorkingShiftModalActive"
                v-model="isAddWorkingShiftModalActive"
                :id="projectId"
        />
        <app-gate-passe-to-project
                v-if="isAddGatePassesModalActive"
                v-model="isAddGatePassesModalActive"
                :id="projectId"
        />
      <app-coordinator-to-project
          v-if="isAddCoordinatorModalActive"
          v-model="isAddCoordinatorModalActive"
          :id="projectId"
      />

      <app-geometry-to-project
          v-if="isAddGeometryModalActive"
          v-model="isAddGeometryModalActive"
          :id="projectId"
      />

    </div>
</template>

<script>

import HelperMixin from "../../../../common/Mixin/Global/HelperMixin";
import ProjectMixin from "../../Mixins/ProjectMixin";
import {PROJECTS} from "../../../Config/ApiUrl";
import coreLibrary from "../../../../core/helpers/CoreLibrary";


export default {
    name: "project",
    extends: coreLibrary,
    mixins: [HelperMixin, ProjectMixin],
    data() {
        return {
            isModalActive: false,
            projectId: '',
            selectedUrl: '',
            message: '',
            isAddEmployeeModalActive: false,
            isAddManagerModalActive: false,
            isAddCoordinatorModalActive: false,
            isAddGeometryModalActive: false,
            isWizardModalActive: false,
            isAddWorkingShiftModalActive: false,
            isAddGatePassesModalActive: false

        }
    },



    methods: {
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${PROJECTS}/${row.id}`;
                this.isModalActive = true;
            } else if (action.name === 'delete') {
                this.delete_url = `${PROJECTS}/${row.id}`;
                this.message = action.message;
                this.confirmationModalActive = true;
            }else if (action.name === 'release') {
                this.getAction(row, action, active);
                //
                // this.selectedUrl = `${PROJECTS}/${row.id}`;
                // console.log(this.selectedUrl);
                // this.message = action.message;
                // this.ReleaseconfirmationModalActive = true;
            }else if (action.name === 'add-employee') {
                this.isAddEmployeeModalActive = true;
                this.projectId = row.id
            }
            else if (action.name === 'add-manager') {
                this.isAddManagerModalActive = true;
                this.projectId = row.id
            }
            else if (action.name === 'add-working-shifts') {
                this.isAddWorkingShiftModalActive = true;
                this.projectId = row.id
            }
            else if (action.name === 'add-gate-passes') {
                this.isAddGatePassesModalActive = true;
                this.projectId = row.id
            }
            else if (action.name === 'add-coordinator') {
                this.isAddCoordinatorModalActive = true;
                this.projectId = row.id
            }
            else if (action.name === 'add-geometry') {
                this.isAddGeometryModalActive = true;
                this.projectId = row.id
            }

        },
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        openWizardModal(){
            this.isWizardModalActive = true;

        },


    },
    computed: {
        ownProject() {
            return window.project.id === this.projectId;
        },

    }
}
</script>
