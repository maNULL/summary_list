import '@fortawesome/fontawesome-free/css/fontawesome.min.css'
import '@fortawesome/fontawesome-free/css/solid.min.css'
import 'leaflet/dist/leaflet.css'
import 'leaflet-draw/dist/leaflet.draw.css'
import 'leaflet.markercluster/dist/MarkerCluster.Default.css'
import 'sidebar-v2/css/leaflet-sidebar.min.css'
import './styles/app.css'

import L from 'leaflet'
import 'leaflet.markercluster'
import 'leaflet.markercluster.layersupport'
import 'leaflet-draw'
import 'sidebar-v2/js/leaflet-sidebar'
import axios from 'axios'
import LayerControlButton from './LayerControlButton'
import { customMarker } from './icons'

const url = 'http://sodch-geofront.it.mvd.ru/osm_tiles/{z}/{x}/{y}.png'

const tileLayer = L.tileLayer(url, { maxZoom: 18 })

const mcgLayerSupportGroup = L.markerClusterGroup.layerSupport({
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
  layers: [tileLayer, mcgLayerSupportGroup],
})

const layerControl = L.control.layers({}, {}, { collapsed: false }).addTo(map)

document
  .getElementsByClassName('leaflet-control-layers')[0]
  .style
  .visibility = 'hidden'

LayerControlButton({ position: 'topleft' }).addTo(map)

const sidebar = L.control.sidebar('sidebar').addTo(map)

const getElementById = (e) => {
  let id = e.target.summaryId

  axios
    .get(`/api/crimes/${id}`)
    .then((response) => {
      let content = document.getElementById('summary-info-content')

      content.innerHTML = `
          <p class="summary-type">${response.data.type}</p>
          <p class="summary-address">${response.data.address}</p>
          <p class="summary-memo">${response.data.memo}</p>
      `
      sidebar.open('summary-info')
    })
    .catch(error => console.log(error.data))
}

axios
  .get('/api/crimes')
  .then((response) => {
    Object
      .keys(response.data)
      .forEach(
        (key) => {
          const layerGroup = L.layerGroup(
            response.data[key].map(e => {
              const marker = L.marker(
                L.latLng(e.latitude, e.longitude), {
                  title: e.type,
                  icon: customMarker(e.markerColor, e.markerIcon),
                },
              )

              marker.summaryId = e.id
              marker.on('click', getElementById)

              return marker
            }),
          )

          mcgLayerSupportGroup.checkIn(layerGroup)
          layerControl.addOverlay(layerGroup, key)
        })
  })

