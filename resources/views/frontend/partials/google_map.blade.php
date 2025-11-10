<script>
    let default_longtitude = '';
    let default_latitude = '';
    @if (get_setting('google_map_longtitude') != '' && get_setting('google_map_longtitude') != '')
        default_longtitude = {{ get_setting('google_map_longtitude') }};
        default_latitude = {{ get_setting('google_map_latitude') }};
    @endif

    // Geocoder global para usar em várias funções
    var geocoder;
    var addressTimeout;

    function initialize(lat = -33.8688, lang = 151.2195, id_format = '') {
        var long = lang;
        var lat = lat;
        if (default_longtitude != '' && default_latitude != '') {
            long = default_longtitude;
            lat = default_latitude;
        }

        // Inicializar Geocoder
        geocoder = new google.maps.Geocoder();

        var map = new google.maps.Map(document.getElementById(id_format + 'map'), {
            center: {
                lat: lat,
                lng: long
            },
            zoom: 13
        });

        var myLatlng = new google.maps.LatLng(lat, long);

        var input = document.getElementById(id_format + 'searchInput');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            map: map,
            position: myLatlng,
            anchorPoint: new google.maps.Point(0, -29),
            draggable: true,
        });

        // Função para fazer geocoding reverso (coordenadas -> endereço)
        function reverseGeocode(latLng) {
            geocoder.geocode({ 'location': latLng }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        var address = results[0].formatted_address;
                        
                        // Preencher o campo textarea com o endereço
                        var addressTextarea = document.querySelector('textarea[name="address"]');
                        if (addressTextarea) {
                            addressTextarea.value = address;
                        }
                        
                        // Atualizar campos de código postal e país
                        for (var i = 0; i < results[0].address_components.length; i++) {
                            if (results[0].address_components[i].types[0] == 'postal_code') {
                                var postalCodeInput = document.querySelector('input[name="postal_code"]');
                                if (postalCodeInput) {
                                    postalCodeInput.value = results[0].address_components[i].long_name;
                                }
                                if (document.getElementById('postal_code')) {
                                    document.getElementById('postal_code').innerHTML = results[0].address_components[i].long_name;
                                }
                            }
                            if (results[0].address_components[i].types[0] == 'country') {
                                if (document.getElementById('country')) {
                                    document.getElementById('country').innerHTML = results[0].address_components[i].long_name;
                                }
                            }
                        }
                        
                        if (document.getElementById('location')) {
                            document.getElementById('location').innerHTML = results[0].formatted_address;
                        }
                        
                        infowindow.setContent('<div><strong>' + address + '</strong></div>');
                        infowindow.open(map, marker);
                    }
                }
            });
        }

        // Click no mapa - move marcador e preenche endereço
        map.addListener('click', function(event) {
            marker.setPosition(event.latLng);
            document.getElementById(id_format + 'latitude').value = event.latLng.lat();
            document.getElementById(id_format + 'longitude').value = event.latLng.lng();
            
            // Fazer geocoding reverso para preencher o endereço
            reverseGeocode(event.latLng);
        });

        // Arrastar marcador - atualiza coordenadas e endereço
        google.maps.event.addListener(marker, 'dragend', function(event) {
            document.getElementById(id_format + 'latitude').value = event.latLng.lat();
            document.getElementById(id_format + 'longitude').value = event.latLng.lng();
            
            // Fazer geocoding reverso para preencher o endereço
            reverseGeocode(event.latLng);
        });

        // Autocompletar do searchInput
        autocomplete.addListener('place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();

            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }

            infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            infowindow.open(map, marker);

            // Preencher o campo textarea com o endereço completo
            var addressTextarea = document.querySelector('textarea[name="address"]');
            if (addressTextarea && place.formatted_address) {
                addressTextarea.value = place.formatted_address;
            }

            //Location details
            for (var i = 0; i < place.address_components.length; i++) {
                if (place.address_components[i].types[0] == 'postal_code') {
                    var postalCodeInput = document.querySelector('input[name="postal_code"]');
                    if (postalCodeInput) {
                        postalCodeInput.value = place.address_components[i].long_name;
                    }
                    if (document.getElementById('postal_code')) {
                        document.getElementById('postal_code').innerHTML = place.address_components[i].long_name;
                    }
                }
                if (place.address_components[i].types[0] == 'country') {
                    if (document.getElementById('country')) {
                        document.getElementById('country').innerHTML = place.address_components[i].long_name;
                    }
                }
            }
            if (document.getElementById('location')) {
                document.getElementById('location').innerHTML = place.formatted_address;
            }
            document.getElementById(id_format + 'latitude').value = place.geometry.location.lat();
            document.getElementById(id_format + 'longitude').value = place.geometry.location.lng();
        });

        // NOVA FUNCIONALIDADE: Listener no campo textarea de endereço
        // Quando o usuário digita o endereço, fazer geocoding e mostrar no mapa
        var addressTextarea = document.querySelector('textarea[name="address"]');
        if (addressTextarea) {
            addressTextarea.addEventListener('input', function(e) {
                var addressValue = e.target.value.trim();
                
                // Usar debounce para não fazer muitas requisições
                clearTimeout(addressTimeout);
                
                if (addressValue.length > 10) { // Mínimo de 10 caracteres
                    addressTimeout = setTimeout(function() {
                        geocoder.geocode({ 'address': addressValue }, function(results, status) {
                            if (status === 'OK') {
                                if (results[0]) {
                                    var location = results[0].geometry.location;
                                    
                                    // Mover o mapa e o marcador para a localização
                                    map.setCenter(location);
                                    map.setZoom(15);
                                    marker.setPosition(location);
                                    marker.setVisible(true);
                                    
                                    // Atualizar latitude e longitude
                                    document.getElementById(id_format + 'latitude').value = location.lat();
                                    document.getElementById(id_format + 'longitude').value = location.lng();
                                    
                                    // Mostrar info window
                                    infowindow.setContent('<div><strong>' + results[0].formatted_address + '</strong></div>');
                                    infowindow.open(map, marker);
                                    
                                    // Atualizar código postal se disponível
                                    for (var i = 0; i < results[0].address_components.length; i++) {
                                        if (results[0].address_components[i].types[0] == 'postal_code') {
                                            var postalCodeInput = document.querySelector('input[name="postal_code"]');
                                            if (postalCodeInput && !postalCodeInput.value) {
                                                postalCodeInput.value = results[0].address_components[i].long_name;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }, 1000); // Esperar 1 segundo após o usuário parar de digitar
                }
            });
        }
    }
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('MAP_API_KEY') }}&libraries=places&language=pt&callback=initialize"
    async defer></script>
