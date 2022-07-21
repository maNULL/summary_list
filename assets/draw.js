import 'leaflet-draw/dist/leaflet.draw.css'
import 'leaflet-draw'
import L from 'leaflet'

export const editableLayers = new L.FeatureGroup()

const drawOptions = {
  draw: {
    marker: false,
    circle: false,
    circlemarker: false,
    polyline: false,
  },
  edit: {
    featureGroup: editableLayers,
    remove: true,
  },
}

export const drawControl = new L.Control.Draw(drawOptions)


