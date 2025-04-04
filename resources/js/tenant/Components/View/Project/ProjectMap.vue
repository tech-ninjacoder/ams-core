<template>

    <div id="app">

        <gmap-map :center="center" :zoom="18" style="width: 100%; height: 500px" ref="map">>
            <gmap-polygon :paths="paths" :editable="false" >
            </gmap-polygon>

        </gmap-map>
    </div>

</template>
<script>
    import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
    import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
    import {axiosGet} from "../../../../common/Helper/AxiosHelper";


    export default {
        name: "app-project-map",
        // mixins: [FormHelperMixins, ModalMixin],
        // el: '#app',
        components: {
            'app-project-details': require('./components/ActivityDetails').default
        },
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
            paths: [],
            props: {

            }
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
            this.getLocation();
            this.getPolygonCenter();

        },
        methods: {
            getLocation(){
                // this.paths = [
                //     [ {lat: 1.380, lng: 103.800}, {lat:1.380, lng: 103.810}, {lat: 1.390, lng: 103.810}, {lat: 1.390, lng: 103.800} ],
                // ];
                axiosGet(`${this.apiUrl.PROJECTS}/${this.props.id}/get-geometry`).then(({data}) => {
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
                axiosGet(`${this.apiUrl.PROJECTS}/${this.props.id}/get-geometry`).then(({data}) => {
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
