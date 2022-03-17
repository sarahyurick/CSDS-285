<?php

  // https://stackoverflow.com/questions/5696412/how-to-get-a-substring-between-two-strings-in-php
  function get_string_between($string, $start, $end) {
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
  }

  if (empty($_GET["url"])) {
          echo "<form action='project1.php' method='GET'>" ;
          echo "<textarea cols=80 rows=10 name='url'>" ;
          echo "paste wikipedia url here" ;
          echo "</textarea>" ;
          echo "<input type=submit>" ;
          echo "</form>" ;
  } else {
          echo "<style> body { margin:10px } </style> <body style='background-color:orchid;'>";
          echo "<h2 style='color:purple;'>HANNAH MONTANA/MILEY CYRUS BEST OF BOTH WORLDS TOUR VISUALIZER</h2>";
          echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>";
          echo "<img style='position:absolute;left:305;top:165;width:850;height:525' src='https://socviz.co/dataviz-pdfl_files/figure-html4/ch-07-firstmap-1.png'/>";

          $url = $_GET["url"] ;
          echo "<p style='color:purple;'>url entered: " . $url . "</p><br>";
          $data = file_get_contents($url);

          $beginning_section = '<h2><span class="mw-headline" id="Tour_dates">Tour dates</span><span class="mw-editsection">' ;
          $end_section = '/table' ;
          $data = strstr($data, $beginning_section) ;
          $data = substr($data, 0, strpos($data, $end_section)) ;

          $pieces = explode('<th scope="row"', $data);
          $pieces = array_slice($pieces, 1);
          foreach($pieces as $i => $key) {
                  $smaller_pieces = explode("<td>", $key);
                  $city = get_string_between($smaller_pieces[1], 'href', 'title');
                  $city = str_replace('="', '', $city);
                  $city = str_replace('" ', '', $city);

                  $city_url = "https://en.wikipedia.org" . $city;
                  $city_coordinates = file_get_contents($city_url);
                  $beginning = 'wgCoordinates';
                  $city_coordinates = strstr($city_coordinates, $beginning);
                  $city_coordinates = str_replace('wgCoordinates":{"lat":', '', $city_coordinates);
                  $city_coordinates = explode("wg", $city_coordinates);
                  $city_coordinates = explode('lon', $city_coordinates[0]);

                  $city_lat = preg_replace("/[^0-9.]/", "", $city_coordinates[0]);
                  $city_lon = preg_replace("/[^0-9.]/", "", $city_coordinates[1]);

                  if($city_lat != 0 and $city_lon != 0){
                          $city_lat = 18 * (50 + -1 * floatval($city_lat)) + 160;
                          $city_lon = 12.5 * (200 + -1 * floatval($city_lon)) - 555;

                          $image_url = 'https://www.pngkey.com/png/full/665-6651419_hannah-montana-png.png';
                          echo "<img style='position:absolute;left:" . $city_lon . ";top:" . $city_lat . ";width:20;height:20' src='" . $image_url . "'/>";
                          # echo "<div style='position:absolute;left:" . $city_lon . ";top:" . $city_lat . ";'>" . $city . "</div>";
                  }
          }
  }

?>
