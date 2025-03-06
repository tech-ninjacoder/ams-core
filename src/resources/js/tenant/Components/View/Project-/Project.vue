<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('projects')">
            <app-default-button
                :title="$fieldTitle('add', 'projects', true)"
                v-if="$can('create_working_shifts')"
                @click="openModal()"
            />
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

export default {
    name: "project",
    mixins: [HelperMixin, ProjectMixin],
    data() {
        return {
            isModalActive: false,
            projectId: '',
            selectedUrl: '',
            message: '',
            isAddEmployeeModalActive: false,
            isAddManagerModalActive: false,
            isAddGeometryModalActive: false

        }
    },

    methods: {
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${PROJECTS}/${row.id}`;
                this.isModalActive = true;
            } else if (action.name === 'delete') {
                this.delete_url = `${PROJECTS}/${row.id}`;
                this.message = action.message;
                this.confirmationModalActive = true;
            }else if (action.name === 'add-employee') {
                this.isAddEmployeeModalActive = true;
                this.projectId = row.id
            }
            else if (action.name === 'add-manager') {
                this.isAddManagerModalActive = true;
                this.projectId = row.id
            }
            else if (action.name === 'add-geometry') {
              this.isAddGeometryModalActive = true;
              this.projectId = row.id
            }
        }
    },
}
</script>
