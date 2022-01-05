
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

let associationAddressInput = document.querySelector('.association-address');

if(associationAddressInput !== null){
    let place = Places({
        container: associationAddressInput
    })

    place.on('change' , e => {
        document.querySelector('#association_postCode').value = e.suggestion.postcode
        document.querySelector('#association_lat').value = e.suggestion.latlng.lat
        document.querySelector('#association_lng').value = e.suggestion.latlng.lng
        document.querySelector('#association_city').value = e.suggestion.county
    } )
}

