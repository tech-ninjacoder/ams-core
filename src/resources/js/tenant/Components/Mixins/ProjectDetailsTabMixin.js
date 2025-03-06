export default {
    data(){
        return{
            projectTabs: [
            ],
            projectDetailsTabs: [

                {
                    'name': this.$t('project_map'),
                    'title': this.$t('project_map'),
                    'component': 'app-project-map',
                    'permission': true,
                    'props': { id: this.projectId}
                },{
                    'name': this.$t('active_employees'),
                    'title': this.$t('active_employees'),
                    'component': 'app-project-active-employees',
                    'permission': true,
                    'props': { id: this.projectId}
                },{
                    'name': this.$t('activity_log'),
                    'title': this.$t('activity_log'),
                    'component': 'app-project-activity',
                    'permission': true,
                    'props': {
                        id: this.projectId
                    }
                },{
                    'name': this.$t('visits_log'),
                    'title': this.$t('visits_log'),
                    'component': 'app-project-visits-employees',
                    'permission': true,
                    'props': {
                        id: this.projectId
                    }
                }

            ]
        }
    }
}
