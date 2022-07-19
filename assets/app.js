import '@fortawesome/fontawesome-free/css/fontawesome.min.css'
import '@fortawesome/fontawesome-free/css/solid.min.css'
import 'leaflet/dist/leaflet.css'
import 'leaflet-draw/dist/leaflet.draw.css'
import 'leaflet.markercluster/dist/MarkerCluster.Default.css'
import './styles/app.css'

// import defaultIcon from 'leaflet/dist/images/marker-icon.png'
import L from 'leaflet'
import 'leaflet.markercluster'
import 'leaflet-draw'
import axios from 'axios'
import { RedMarker } from './icons'

const url = 'http://sodch-geofront.it.mvd.ru/osm_tiles/{z}/{x}/{y}.png'

const tileLayer = L.tileLayer(url, { maxZoom: 18 })

const crimeLayer = L.markerClusterGroup({
  spiderfyOnMaxZoom: true,
  showCoverageOnHover: false,
  zoomToBoundsOnClick: true,
})

const map = L.map('map', {
  center: [44.8632577, 43.4406913],
  crs: L.CRS.EPSG3857,
  zoom: 8,
  zoomControl: true,
  preferCanvas: false,
  layers: [tileLayer, crimeLayer],
})

axios
  .get('/api/get_crimes')
  .then(function (response) {
    const data = response.data

    for (let i = 0; i < data.length; i++) {
      const location = L.latLng(data[i].latitude, data[i].longitude)
      const title = data[i].id

      const marker = L
        .marker(
          location, {
            title: title,
            icon: RedMarker,
          },
        ).bindPopup(title)
      crimeLayer.addLayer(marker)
    }
  })

