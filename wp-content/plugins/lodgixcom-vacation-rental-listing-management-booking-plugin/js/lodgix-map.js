var LodgixMap = (function ($) {
    'use strict';

    return function (options) {

        this.options = options;

        this.init = function () {
            this.leafletMap = L.map(this.options.id, {
                center: [this.options.lat, this.options.lon],
                zoom: this.options.zoom
            });
            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.leafletMap);
            L.marker([this.options.lat, this.options.lon]).addTo(this.leafletMap);
        };

        this.initOnDocumentReady = function () {
            var that = this;
            $(document).ready(function () {
                that.init();
            });
        };

        this.setZoom = function (value) {
            value = parseInt(value);
            if (!isNaN(value) && value > 0) {
                if (this.leafletMap) {
                    this.leafletMap.setZoom(value);
                }
            }
        };

    };

})(window.jQLodgix);
