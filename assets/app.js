import './styles/app.css'
import 'leaflet/dist/leaflet.css'

import L from 'leaflet'

const url = 'http://sodch-geofront.it.mvd.ru/osm_tiles/{z}/{x}/{y}.png'

const tileLayer = L.tileLayer(url, { maxZoom: 18 })

const map = L.map('map', {
  center: [44.8632577, 43.4406913],
  crs: L.CRS.EPSG3857,
  zoom: 8,
  zoomControl: true,
  preferCanvas: false,
  layers: [tileLayer],
})

