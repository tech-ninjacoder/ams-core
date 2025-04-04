<template>
    <div class="media align-items-center">
        <div class="media-body ml-3">
            <a v-if="rowData.name" :href="profileUrl">{{ rowData.name }}</a>
            <a v-else :href="profileUrl">{{ rowData.email }}</a>
        </div>
    </div>
</template>
<script>
import {PROJECTS_OBJECT} from "../../../../Config/ApiUrl";
import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "ProjectObject",
    props: {
        value: {},
        rowData: {},
        index: {},
    },
    computed: {
        profileUrl() {
            // return urlGenerator(`${EMPLOYEES_PROFILE}/${this.rowData.id}/profile`)
            return urlGenerator(`${PROJECTS_OBJECT}/${this.rowData.id}`)

        }
    }
}
</script>
