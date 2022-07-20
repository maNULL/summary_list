import '@fortawesome/fontawesome-free/css/fontawesome.min.css'
import '@fortawesome/fontawesome-free/css/solid.min.css'
import L from 'leaflet'

export function customMarker (color, classes, size = 20) {
  return L.divIcon({
    html: `<i style="color: ${color}" class="fa fa-3x ${classes}"></i>`,
    iconSize: [size, size],
    className: '',
  })
}