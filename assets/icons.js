import '@fortawesome/fontawesome-free/css/fontawesome.min.css'
import '@fortawesome/fontawesome-free/css/solid.min.css'
import L from 'leaflet'

const marker = (color, size = 20) => L.divIcon({
  html: `<i style="color: ${color}" class="fa fa-solid fa-location-pin fa-3x"></i>`,
  iconSize: [size, size],
  className: '',
})

const RedMarker = marker('red')
const GreenMarker = marker('green')

export { RedMarker, GreenMarker }