<template>
    <modal-full-width id="project-geometry-modal"
           v-model="showModal"
           :title="$addLabel('Location')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
           :scrollable="false"
    >
        <form
            :data-url='`${apiUrl.PROJECTS}/${id}/add-geometry`'
            method="POST"
            ref="form"
        >
<!--            <app-note-->
<!--                class="mb-4"-->
<!--                :title="$t('note')"-->
<!--                :notes="$can('update_working_shifts') ?-->
<!--                      $t('this_workshift_is_read_only_due_to_attendance_history') :-->
<!--                      $t('working_shift_update_note')"-->
<!--            />-->
<!--            <app-form-group-selectable-->
<!--                type="multi-select"-->
<!--                :label="$t('employee')"-->
<!--                list-value-field="full_name"-->
<!--                v-model="formData.users"-->
<!--                :error-message="$errorMessage(errors, 'users')"-->
<!--                :fetch-url="`${apiUrl.TENANT_SELECTABLE_USER}?without=admin&employee=only`"-->
<!--            />-->
            <div id="app">

                    <label>
                        Start at:
                        <gmap-autocomplete @place_changed="updateCenter($event)" />
                    </label>
            <gmap-map :center="center" :zoom="18" style="width: 100%; height: 500px" ref="map">>
                <gmap-polygon :paths="paths" :editable="true" @paths_changed="updateEdited($event)">
                </gmap-polygon>

            </gmap-map>

<!--                <ul v-if="edited" @click="edited = null">-->
<!--                    <li v-for="path in edited">-->
<!--                        <ol>-->
<!--                            <li v-for="point in path">-->
<!--                                {{point.lat}}, {{point.lng}}-->
<!--                            </li>-->
<!--                        </ol>-->
<!--                    </li>-->
<!--                </ul>-->
            </div>
        </form>
        <div>
            <button @click="addPath()">Draw Basic Geofence</button>
        </div>
    </modal-full-width>
</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";



export default {
    name: "AddGeometryToProject",
    mixins: [FormHelperMixins, ModalMixin],
    // el: '#app',
    props: {
        id: {
            required: true
        },
        center: {
            type: Object,
            default: {
                lat: 1.38, lng: 103.8
            }
        },

        edited: null,
        paths: [
            [ {lat: 1.380, lng: 103.800}, {lat:1.380, lng: 103.810}, {lat: 1.390, lng: 103.810}, {lat: 1.390, lng: 103.800} ],
            [ {lat: 1.382, lng: 103.802}, {lat:1.382, lng: 103.808}, {lat: 1.388, lng: 103.808}, {lat: 1.388, lng: 103.802} ],
        ],
    },

    data() {
        return {
            formData: {
                // users: [],
                Geometry:[],
                // paths :[],


            }
        }
    },
    mounted() {
        this.preloader = true;
        this.getProjectUsers();
        this.getLocation();
        this.getPolygonCenter();
    },
    methods: {
        submit() {
            this.loading = true;
            this.message = '';
            this.errors = {};

            let formData = {...this.formData};
            // if (this.formData.type === 'regular') {
            //     formData = this.prepareRegularType(this.formData);
            // }
            // else {
            //     formData = this.prepareSchedulerType(this.formData);
            // }

            // formData.name = formatDateForServer(formData.start_date);
            // formData.end_date = formatDateForServer(formData.end_date);
            // this.makeDefault ? formData.is_default = 1 : null;

            this.save(formData);
        },
        updateCenter: function (place) {
            this.center = {
                lat: place.geometry.location.lat(),
                lng: place.geometry.location.lng(),
            }
        },


        afterSuccess({data}) {
            this.formData = {Geometry: []};
            $('#project-geometry-modal').modal('hide')
            this.toastAndReload(data.message, 'project-table');
        },
        getProjectUsers() {
            axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/users`).then(({data}) => {
                this.preloader = false;
                this.formData.users = data;
            })
        },
        getLocation(){
            // this.paths = [
            //     [ {lat: 1.380, lng: 103.800}, {lat:1.380, lng: 103.810}, {lat: 1.390, lng: 103.810}, {lat: 1.390, lng: 103.800} ],
            // ];
            axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/get-geometry`).then(({data}) => {
                this.preloader = false;
                this.paths = data;
            })

        },
        addPath: function () {
            // obtain the bounds, so we can guess how big the polygon should be
            var bounds = this.$refs.map.$mapObject.getBounds()
            var northEast = bounds.getNorthEast()
            var southWest = bounds.getSouthWest()
            var center = bounds.getCenter()
            var degree = this.paths.length + 1;
            var f = Math.pow(0.66, degree)

            // Draw a triangle. Use f to control the size of the triangle.
            // i.e., every time we add a path, we reduce the size of the triangle
            var path = [
                { lng: center.lng(), lat: (1-f) * center.lat() + (f) * northEast.lat() },
                { lng: (1-f) * center.lng() + (f) * southWest.lng(), lat: (1-f) * center.lat() + (f) * southWest.lat() },
                { lng: (1-f) * center.lng() + (f) * northEast.lng(), lat: (1-f) * center.lat() + (f) * southWest.lat() },
            ]

            this.paths = path;
        },
        updateEdited(mvcArray) {
            let paths = [];
            let Geometry = [];

            for (let i=0; i<mvcArray.getLength(); i++) {
                let path = [];
                for (let j=0; j<mvcArray.getAt(i).getLength(); j++) {
                    let point = mvcArray.getAt(i).getAt(j);
                    path.push({lat: point.lat(), lng: point.lng()});
                }
                paths.push(path);
                this.formData.Geometry = path;

            }
            this.edited = paths;
        },
        getPolygonCenter(){
            // var bounds = this.paths.getBounds()
            // var path = bounds.getCenter()
            let path = [{lat: 1.380, lng: 103.800}, {lat:1.380, lng: 103.810}, {lat: 1.390, lng: 103.810}, {lat: 1.390, lng: 103.800}];

            // console.log(axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/get-geometry`));


            var centers = function (arr) {
                var minX, maxX, minY, maxY;
                for (var i = 0; i < arr.length; i++)
                {
                    var x = arr[i]['lat'], y = arr[i]['lng'];
                    minX = (x < minX || minX == null) ? x : minX;
                    maxX = (x > maxX || maxX == null) ? x : maxX;
                    minY = (y < minY || minY == null) ? y : minY;
                    maxY = (y > maxY || maxY == null) ? y : maxY;
                }
                return [(minX + maxX) / 2, (minY + maxY) / 2];
            };
            axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/get-geometry`).then(({data}) => {
                var cal = centers(data);
                var centerx = {
                    lat: cal[0], lng: cal[1]
                };
                this.center = centerx ;
                console.log(centerx);

            })

            // this.center = centerx;

            // console.log(this.paths);



        },
    },
}
</script>
