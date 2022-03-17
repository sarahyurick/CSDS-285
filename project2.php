<?php

# playlist API:  https://developers.google.com/youtube/v3/docs/playlistItems/list?apix=true#usage
# quota calculator:  https://developers.google.com/youtube/v3/determine_quota_cost?__hstc=20629287.bcd2ff33136b804dec7cbf541860d7f1.1607375793231.1613067324717.1613074986633.160&__hssc=20629287.14.1613074986633&__hsfp=2515021167
# free movies playlist:  https://www.youtube.com/playlist?list=PLHPTxTxtC0ibVZrT2_WKWUl2SAxsKuKwx

# FUTURE WORK: maybe allow it to work with multiple playlists

if (empty($_GET["playlist_id"])) {
        echo "<h1>Enter the Playlist ID of a YouTube movies playlist</h1>";
        echo "<br>For example, <a href='https://www.youtube.com/playlist?list=PLHPTxTxtC0ibVZrT2_WKWUl2SAxsKuKwx'>https://www.youtube.com/playlist?list=PLHPTxTxtC0ibVZrT2_WKWUl2SAxsKuKwx</a> has Playlist ID PLHPTxTxtC0ibVZrT2_WKWUl2SAxsKuKwx";
        echo "<br>Results are limited to the first 25 items in the playlist.<br>";
        echo "<form action='project2.php' method='GET'>";
        echo "<textarea cols=80 rows=1 name='playlist_id'></textarea>";
        echo "<input type=submit>";
        echo "</form>";

} else {

# if I didn't want to call the API every time, I could read from a JSON instead
# $json = "/home/sey13/public_html/movies.json";
# $string = file_get_contents($json);

# calls YouTube API to get all of the videos in the playlist
# $playlist_id = "PLHPTxTxtC0ibVZrT2_WKWUl2SAxsKuKwx";
$playlist_id = $_GET["playlist_id"];
$string = shell_exec("bash call_api.bash " . $playlist_id);
$json_a = json_decode($string, true);

# FUTURE WORK: maybe something to handle a playlist of trailers
#              or a mixture of movies and trailers

$titles = array();  # array of movie titles
$videos = array();  # array of movie URLs
# parsing through the JSON
foreach ($json_a as $k0 => $v0) {
        if ($k0 == "items") {  # we only care about the list of movies
                foreach ($v0 as $k1 => $v1) {
                        foreach ($v1 as $k2 => $v2) {
                                if ($k2 == "snippet") {  # getting movie metadata
                                        foreach ($v2 as $k3 => $v3) {
                                                if ($k3 == "title") {  # getting the movie name
                                                        # echo $v3, "\n";
                                                        # FUTURE WORK: clean video name so that we can work more flexibly e.g. with trailers
                                                        #              Also need to filter out private/unlisted videos
                                                        array_push($titles, $v3);
                                                } else if ($k3 == "resourceId") {
                                                        foreach ($v3 as $k4 => $v4) {
                                                                if ($k4 == "videoId") {  # getting the YouTube URL
                                                                        # echo $v4 . "\n";
                                                                        array_push($videos, $v4);
                                                                }
                                                        }
                                                }
                                        }
                                }
                        }
                }
        }
}

# IMDb API only allows 1000 requests per day :(
# so it looks like I'm doing this the hard way
$links = array();  # array of IMDb links
foreach ($titles as $k => $title) {
        # echo $title;
        # search for the movie on IMDb
        $title = str_replace(" ", "+", $title);
        $title_search = "https://www.imdb.com/find?q=" . $title . "&ref_=nv_sr_sm";
        $search_results = file_get_contents($title_search);

        # grab the first search result and isolate the link to that movie's IMDb page
        # FUTURE WORK: check IMDb year against YouTube year to ensure that we have the right movie.
        #              this would involve calling the YouTube API for each movie to get the release year
        #              and I don't want to worry about going over my quota
        $pieces = explode('<tr class="findResult', $search_results);
        $piece = array_slice($pieces, 1, 1)[0];
        $piece = strstr($piece, '<a href="');
        $piece = str_replace('<a href="', '', $piece);
        $link = strstr($piece, '" ><img', true);
        array_push($links, $link);

        # FUTURE WORK: extract movie poster images to make our webservice prettier
}

$ratings_titles = array();  # array of arrays of movie titles, with the rating as the key
$ratings_videos = array();  # array of arrays of YouTube links, with the rating as the key
$ratings_links  = array();  # array of arrays of IMDb links, with the rating as the key
foreach ($links as $k => $link) {
        # load the IMDb page contents for the movie and isolate the rating
        $movie_page = file_get_contents("https://www.imdb.com" . $link);
        $pieces = explode('<span class="sc-7ab21ed2-1 jGRxWM">', $movie_page);
        $piece = array_slice($pieces, 1, 1)[0];
        $rating = strstr($piece, "</span>", true);

        # use array of arrays so that we can handle movies with the same rating
        if (array_key_exists($rating, $ratings_titles)) {
                array_push($ratings_titles[$rating], $titles[$k]);
                array_push($ratings_videos[$rating], $videos[$k]);
                array_push($ratings_links[$rating] , $links[$k]) ;
        } else {
                $ratings_titles[$rating] = array($titles[$k]);
                $ratings_videos[$rating] = array($videos[$k]);
                $ratings_links[$rating]  = array($links[$k]) ;
        }

        # FUTURE WORK: include the option to sort by number of ratings
}

# sort by best to worst rating
krsort($ratings_titles);
krsort($ratings_videos);
krsort($ratings_links) ;

# FUTURE WORK: allow user to only obtain the top n movies

# print_r($ratings_titles);
# display the results to the user!
echo "<h1>THE BEST MOVIES ON YOUTUBE</h1>";

echo "<br>Enter the Playlist ID of a YouTube movies playlist. For example, <a href='https://www.youtube.com/playlist?list=PLHPTxTxtC0ibVZrT2_WKWUl2SAxsKuKwx'>https://www.youtube.com/playlist?list=PLHPTxTxtC0ibVZrT2_WKWUl2SAxsKuKwx</a> has Playlist ID PLHPTxTxtC0ibVZrT2_WKWUl2SAxsKuKwx";
echo "<br>Results are limited to the first 25 items in the playlist.<br>";
echo "<form action='project2.php' method='GET'>";
echo "<textarea cols=80 rows=1 name='playlist_id'></textarea>";
echo "<input type=submit>";
echo "</form>";

foreach ($ratings_titles as $k0 => $titles_array) {
        foreach ($titles_array as $k1 => $title) {
                echo "Movie: " . $title . "<br>IMDb Rating: " . $k0 . "<br><a href='https://youtu.be/" . $ratings_videos[$k0][$k1] . "'>YouTube link</a><br><a href='https://www.imdb.com" . $ratings_links[$k0][$k1] . "'>IMDb link</a><br><br>";
        }
}

}  # end else block

?>
