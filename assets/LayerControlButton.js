import L from 'leaflet'

L.Control.LayersButton = L.Control.extend({
  onAdd: () => {
    const container = L.DomUtil.create('div', 'leaflet-bar')
    const link = L.DomUtil.create('a', '', container)
    link.innerHTML = '<i class="fa fa-layer-group"></i>'
    link.href = '#'
    link.title = 'Типы происшествий'

    L.DomEvent
     .on(link, 'click', (event) => {
       event.preventDefault()

       const leafletControlLayers = document.getElementsByClassName(
         'leaflet-control-layers')[0]

       if ('hidden' === leafletControlLayers.style.visibility) {
         leafletControlLayers.style.visibility = 'visible'
         return false
       }

       if ('visible' === leafletControlLayers.style.visibility) {
         leafletControlLayers.style.visibility = 'hidden'
         return false
       }
     })

    return container
  },
})

export default L.Control.layersButton = (options) =>
  new L.Control.LayersButton(options)