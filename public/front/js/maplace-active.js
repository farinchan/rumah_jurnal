$(function() {
               
            var LocsA = [
                {
                    lat: 40.740178,
                    lon: -74.190194,
                    title: 'Location 1',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/1.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.733617,
                    lon: -74.171150,
                    title: 'Location 2',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/2.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.743011,
                    lon: -74.247100,
                    title: 'Location 3',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/3.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.711150,
                    lon: -74.214998,
                    title: 'Location 4',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/4.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.690010,
                    lon: -74.151753,
                    title: 'Location 5',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/5.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.697590,
                    lon: -74.263164,
                    title: 'Location 6',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/6.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.729979,
                    lon: -74.271992,
                    title: 'Location 7',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/1.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.749702,
                    lon: -74.163631,
                    title: 'Location 8',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/2.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.718971,
                    lon: -74.323219,
                    title: 'Location 9',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/3.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.748350,
                    lon: -74.323219,
                    title: 'Location 10',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/4.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                },
                {
                    lat: 40.740178,
                    lon: -74.190194,
                    title: 'Location 11',
                    html: [ '<div class="ltn__map-product-item">',
                                '<a href="#"><img src="img/product-3/5.jpg" alt="#"></a>',
                                '<h5 class="ltn__map-product-title"><a href="#">House in Upper East Side</a></h5>',
                                '<h5 class="ltn__map-product-price">$30,000</h5>',
                                '<p class="ltn__map-product-info"><span>3 Bed</span><span>3 Bath</span><span>1220 ft<sup>2</sup></span></p>',
                                '<p class="ltn__map-product-location"><i class="flaticon-pin"></i>Boston, New York</span>',
                            '</div>'
                        ].join(''),
                    icon: 'img/icons/map-marker-2.png',
                    animation: google.maps.Animation.BOUNCE
                }

            ];
            new Maplace({
                locations: LocsA,
                controls_on_map: true,
                map_options: {
                    zoom: 13,
                    scrollwheel: false,
                    stopover: true
                },
                stroke_options: {
                    strokeColor: '#f10', 
                    strokeOpacity: 0.8, 
                    strokeWeight: 2, 
                    fillColor: '#f10', 
                    fillOpacity: 0.4
                }
            }).Load();

        });