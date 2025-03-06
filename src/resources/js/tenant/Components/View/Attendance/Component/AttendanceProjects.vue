<template>
    <ProjectName
        :project="project"
    />
</template>

<script>
import DateTimeWithNote from "./AttendanceRequest/DateTimeWithNote";
import {filterLastPendingData} from "../Helper/Helper";
import ProjectName from "./AttendanceRequest/ProjectName";

export default {
    name: "AttendanceProjects",
    components: {ProjectName},
    props: {
        value: {},
        tableId: {},
    },
    computed: {
        details() {
            return {};
        },
        project() {
            if (this.details.projects) {
                return this.collection(this.details.projects).first();
            }
            console.log(this.details.comments);

            return {};
        }
    }
}
</script>

<style scoped>

</style>
