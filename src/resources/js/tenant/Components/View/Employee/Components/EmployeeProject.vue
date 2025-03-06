<template>
    <div class="d-flex align-items-center" :class="alignmentClass">
        <span
            :class="`badge badge-pill badge-${rowData.projects ? rowData.projects.class : ''} text-capitalize`">
            {{ projectName }}
        </span>
    </div>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";

export default {
    name: "EmployeeProjects",
    mixins: [FormHelperMixins],
    props: {
        rowData: {},
        projectName: {},
        alignmentClass: {}
    },
    computed: {
        modifier: (projects) => {
            let projectName = '';
            projects.forEach((element, index) => {
                projectName = index > 0 ? ', ' + element.name : element.name;
            });
            projectName = projectName ? projectName : '-';
            return projectName;
        }

    }
}
</script>
