<?php
include '_base.php';

// ----------------------------------------------------------------------------



// ----------------------------------------------------------------------------

$_title = '';
include '_head.php';
?>

<head>
    <link rel="stylesheet" href="/css/index.css">
</head>

<div id="image-container">
    <img id="img" src="/images/1.png">
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPACeYLYtCvDsSBexeiij5AL6FR6wQzqk&callback=initMap" async defer></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const arr = [
        '/images/1.png',
        '/images/2.png',
        '/images/3.png',
        '/images/4.png',
    ]

    let i = 0;

    arr.forEach(src => {
        const img = new Image();
        img.src = src;
    });

    function changeImage() {
        const imgElement = $('#img');

        imgElement.addClass('fade-out');

        setTimeout(() => {
            i = ++i % arr.length;
            imgElement.prop('src', arr[i]);
        }, 800);
    }

    $('#img').on('load', function(){
        $(this).removeClass('fade-out');
    });

    setInterval(changeImage, 3000);

    $('#img').on('click', changeImage);

    // Google Map
    function initMap() {
    const storeLocation = { lat: 26.0141, lng: 28.1079 };

    const map = new google.maps.Map(document.getElementById("store-map"), {
        zoom: 15,
        center: storeLocation,
        styles: [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
        ]
    });

    const marker = new google.maps.Marker({
        position: storeLocation,
        map: map,
        title: "Yobisual Store Location",
        icon: {
            url: "/images/map-marker.png",
            scaledSize: new google.maps.Size(40, 40)
        }
    });

    const infoWindow = new google.maps.InfoWindow({
        content: "<h3>Yobisual Store</h3><p>KK Times Square</p>"
    });

    marker.addListener('click', () => {
        infoWindow.open(map, marker);
    });

    infoWindow.open(map, marker);
}

window.initMap = initMap;

   
</script>

<section class="about-section">
    <h2>About Us</h2>
    <p>Welcome to the official online Yobisual Designer Store</p>
    <p>Founded in 2022, with a motive of providing highest standards of quality and sustainability clothing hassle-free</p>

    <div class = "team-members">
        <div class = "team-member">
            <img src = "images/Designer.jpg"> 
                <h3>Yohji Ng</h3>
                <p>Founder & Ceo</p>
        </div>
        <div class = "team-member">
            <img src = "images/design.png"> 
                <h3>Isaac Yung</h3>
                <p>Head of Design</p>
        </div>
        <div class = "team-member">
            <img src = "images/ceo.png"> 
                <h3>Alvan Chin</h3>
                <p>Marketing Director</p>
        </div>
        <div class="team-member">
            <img src="images/hr.jpg">
                <h3>Lourenza Pea</h3>
                <p>Head of HR</p>
        </div>
    </div>
</section>

<section class="map-section">
    <h2>Visit Our Store</h2>
    <div class="map-info">
        <p>Visit our physical store at:</p>
        <p><strong>KK Times Square Lot 21-9, Imago, Kota Kinabalu</strong></p>
        <p>Open Daily: 10am-10pm</p>
    </div>

    <div class="map-container" id="store-map"></div>

    <div class="map-info">
        <p>Accessible Parking | Free WIFI</p>
    </div>
</section>



<?php
include '_foot.php';