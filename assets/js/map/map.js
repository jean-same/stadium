import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';

mapboxgl.accessToken = 'pk.eyJ1IjoidGFibGV0dGUiLCJhIjoiY2t3dDBjMmJxMWJrYTJubGM4YnBkdjBhdCJ9.4RMFYrXv1CD69JkIImCwVg';

let mapDiv = document.querySelector('#map');

if(mapDiv != null ){
    let lat = mapDiv.dataset.lat;
    let lng = mapDiv.dataset.lng;
    let associationName = mapDiv.dataset.associationname


const map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11',
    center: [
        lng , lat
    ],
    zoom: 15
    });

    const popup = new mapboxgl.Popup({ offset: 35 }).setText(
        associationName
        );

    const marker = new mapboxgl.Marker({ color: '#074666'}).setLngLat([lng, lat]).setPopup(popup).addTo(map);

    }