
import Places from 'places.js';

let inputAddress = document.querySelector('.search_input');

if(inputAddress !== null ){
    let place = Places({
        container: inputAddress
    })

    place.on('change' , e => {
        document.querySelector('#association_search_lat').value = e.suggestion.latlng.lat
        document.querySelector('#association_search_lng').value = e.suggestion.latlng.lng
    } )
}

