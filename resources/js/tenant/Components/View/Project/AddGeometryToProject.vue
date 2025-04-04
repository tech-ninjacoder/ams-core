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
            <div id="app">

                    <label>
                        Start at:
                        <gmap-autocomplete @place_changed="updateCenter($event)" />
                    </label>
            <gmap-map :center="center" :zoom="18" style="width: 100%; height: 500px" ref="map">>
                <gmap-polygon :paths="paths" :editable="true" @paths_changed="updateEdited($event)">
                </gmap-polygon>
                <gmap-polygon v-for="(path, index) in backgroundPaths" :key="index" :paths="path.paths" :editable="false" :options="{fillColor: 'green', strokeColor: 'red', strokeWeight: 2}" :infoWindow="{content: path.name}">
                </gmap-polygon>

            </gmap-map>

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
        ],backgroundPaths: [
            [
                {lat: 25.33071954305722, lng: 55.47567445208434},
                {lat: 25.319734518545747, lng: 55.470043422478454},
                {lat: 25.31612674421405, lng: 55.475728096264596},
                {lat: 25.312130911738517, lng: 55.481391312378655},
                {lat: 25.323251267697803, lng: 55.48936055769327},
                {lat: 25.325371450772195, lng: 55.42505601455574}
            ],
            // [{"lat":25.22871954305722,"lng":55.48567445208434},{"lat":25.319734518545747,"lng":55.470043422478454},{"lat":25.31612674421405,"lng":55.475728096264596},{"lat":25.312130911738517,"lng":55.481391312378655},{"lat":25.323251267697803,"lng":55.48936055769327},{"lat":25.325371450772195,"lng":55.48505601455574}]

            // [ {lat: 1.382, lng: 103.802}, {lat:1.382, lng: 103.808}, {lat: 1.388, lng: 103.808}, {lat: 1.388, lng: 103.802} ],
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
        this.getBackgroundPaths();
    },
    methods: {
        submit() {
            this.loading = true;
            this.message = '';
            this.errors = {};

            let formData = {...this.formData};

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
            axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/get-geometry`).then(({data}) => {
                this.preloader = false;
                this.paths = data;
                // console.log(JSON.stringify(this.paths))
            })


        },
        getBackgroundPaths(){
            axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/get-background-polygons`).then(({data}) => {
                this.preloader = false;
                const paths = [];
                data.forEach((project) => {
                    const polygon = {
                        name: project.name,
                        paths: project.geometry,
                    };
                    paths.push(polygon);
                });
                this.backgroundPaths = paths;
            });
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
