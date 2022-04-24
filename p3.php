<?php

// http://eecslab-22.case.edu/~ycg/project3/p3.php
// http://eecslab-22.case.edu/~sey13/project3/p3.php

echo '<head>
<style>
* {
box-sizing: border-box;
}
.column {
float: left;
width: 33.33%;
padding: 5px;
}
.row::after {
content: "";
clear: both;
display: table;
}
</style>
</head>';

if (empty($_GET["username"])) {
    echo "<center> <h2> CSDS 285 Project 3: Social Media Following Stats </h2> </center>";
    echo '<center> <div class="row">
        <div class="column">
        <a href="https://www.twitter.com"><img src="https://1000logos.net/wp-content/uploads/2021/04/Twitter-logo.png" style="width:100">
        </a></div>
        <div class="column">
        <a href="https://www.letterboxd.com"><img src="https://a.ltrbxd.com/logos/letterboxd-mac-icon.png" style="width:100">
        </a></div>
        <div class="column">
        <a href="https://www.github.com"><img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" style="width:100">';
    echo "</a></div></div>";
    echo "<br>";
    echo "<center> Select a social media platform, enter your username, and click submit to find out who does not follow you back </center>";
    echo "<center> If your username is @abcdef, enter abcdef </center>";
    echo "<center> Limited to users who have following and follower counts less than 500";
    echo "<br>";
    echo "<br>";

    echo "<form action='p3.php' method='GET'>";
    echo "<label for='site'>Choose a site:</label>";
    echo "<select name='site' id='site'>";
    echo "<option value='Twitter'>Twitter</option>";
    echo "<option value='Letterboxd'>Letterboxd</option>";
    echo "<option value='GitHub'>GitHub</option>";
    echo "</select>";
    echo "<br><br>";

    echo "<br>";
    echo "<br>";
    echo "<center>";
    echo " <textarea cols=60 rows=1 name='username'>";
    echo "</textarea>";
    echo "<center> <input type=submit style= 'margin-top: 25px; height: 3%; width:5%'>";
    echo "</form>" ;
    echo "</center>";
}

else {
    $username = $_GET["username"];
    $site = $_GET["site"];

    if ($site == "Twitter") {
        // Get Twitter ID using username
        $userDataCurl = shell_exec('curl "https://api.twitter.com/2/users/by/username/' . $username . '" -H "Authorization: Bearer AAAAAAAAAAAAAAAAAAAAACTqaQEAAAAAwqi8%2B3Ah%2F2kc2BiDytd2ZqRqJmA%3DW6CDyJYHfcRDZlfdynNGQY7LIwp32rDlGTBimQiVl1qK7rGAMg"');
        $userData = json_decode($userDataCurl, true);
        $userID = $userData['data']['id'];

        echo "<center> <h2> CSDS 285 Project 3: Twitter Following Stats </h2>";
        echo "<br>";
        echo '<center><a href="https://www.twitter.com"><img src="https://1000logos.net/wp-content/uploads/2021/04/Twitter-logo.png" style="width:100"></a>';
        echo "<br>";
        echo "<br>";
        echo "<br>";

        echo "<form action='p3.php' method='GET'>";
        echo "<label for='site'>Choose a site:</label>";
        echo "<select name='site' id='site'>";
        echo "<option value='Twitter'>Twitter</option>";
        echo "<option value='Letterboxd'>Letterboxd</option>";
        echo "<option value='GitHub'>GitHub</option>";
        echo "</select>";
        echo "<br><br>";

        echo "<br>";
        echo "<br>";
        echo "<center>";
        echo " <textarea cols=60 rows=1 name='username'>";
        echo "</textarea>";
        echo "<center> <input type=submit style= 'margin-top: 25px; height: 3%; width:5%'>";
        echo "</form>" ;
        echo "</center>";

        echo "<center> <h3> Username: </h3>" .$username . "<h3> UserID: </h3>" . $userID . "</center>";

        // Get followers from ID
        $followersCurl = shell_exec('curl "https://api.twitter.com/2/users/'.$userID.'/followers?max_results=500" -H "Authorization: Bearer AAAAAAAAAAAAAAAAAAAAACTqaQEAAAAAwqi8%2B3Ah%2F2kc2BiDytd2ZqRqJmA%3DW6CDyJYHfcRDZlfdynNGQY7LIwp32rDlGTBimQiVl1qK7rGAMg"');

        echo "<br>";

        $followers = json_decode($followersCurl, true);
        $followersData = $followers['data'];

        $followerUsernames = array();
        foreach($followersData as $element) {
            array_push($followerUsernames, $element['username']);
        }

        // Get following from ID
        $followingCurl = shell_exec('curl "https://api.twitter.com/2/users/' . $userID . '/following?max_results=500" -H "Authorization: Bearer AAAAAAAAAAAAAAAAAAAAACTqaQEAAAAAwqi8%2B3Ah%2F2kc2BiDytd2ZqRqJmA%3DW6CDyJYHfcRDZlfdynNGQY7LIwp32rDlGTBimQiVl1qK7rGAMg"');

        $following = json_decode($followingCurl, true);
        $followingData = $following['data'];

        $followingUsernames = array();
        foreach($followingData as $element) {
            array_push($followingUsernames, $element['username']);
        }

        // Printing followers and following
        $followingString = "";
        foreach ($followingUsernames as $i) {
            $followingString = $followingString . "<a href='https://www.twitter.com/" . $i . "'>" . $i . "</a><br>";
        }
        $followersString = "";
        foreach($followerUsernames as $i) {
            $followersString = $followersString . "<a href='https://www.twitter.com/" . $i . "'>" . $i . "</a><br>";
        }

        // Finding the people you follow that dont follow you
        $diff = array_diff($followingUsernames, $followerUsernames);
        $diffString = "";
        foreach ($diff as $i) {
            $diffString = $diffString . "<a href='https://www.twitter.com/" . $i . "'>" . $i . "</a><br>";
        }
        if (strlen($username) != 0) {
            echo "<br>";
            echo "<center>";
            echo "<table>";
            echo "<tr><th><h3>Following</h3></th><th><h3>Followers</h3></th><th><h3>Not Following You Back</h3></th></tr>";
            echo "<tr><td style='padding:60px; vertical-align: top; padding-top: 0em;'>$followingString</td><td style='padding:60px; vertical-align: top; padding-top: 0em;'>$followersString</td><td style='padding:60px;vertical-align: top; padding-top: 0em;'>$diffString</td></tr>";
            echo "</table>";
            echo "</center>";
        }
    }

    else if ($site == "Letterboxd") {
        echo "<center> <h2> CSDS 285 Project 3: Letterboxd Following Stats </h2>";
        echo '<center><a href="https://www.letterboxd.com"><img src="https://a.ltrbxd.com/logos/letterboxd-mac-icon.png" style="width:100"></a>';
        echo "<br>";
        echo "<br>";
        echo "<br>";

        echo "<form action='p3.php' method='GET'>";
        echo "<label for='site'>Choose a site:</label>";
        echo "<select name='site' id='site'>";
        echo "<option value='Letterboxd'>Letterboxd</option>";
        echo "<option value='Twitter'>Twitter</option>";
        echo "<option value='GitHub'>GitHub</option>";
        echo "</select>";
        echo "<br><br>";

        echo "<br>";
        echo "<br>";
        echo "<center>";
        echo " <textarea cols=60 rows=1 name='username'>";
        echo "</textarea>";
        echo "<center> <input type=submit style= 'margin-top: 25px; height: 3%; width:5%'>";
        echo "</form>" ;
        echo "</center>";

        echo "<center> <h3> Username: </h3>" .$username;
        echo "<br><br>";

        $url = "https://www.letterboxd.com/" . $username . "/followers/";
        $followers = file_get_contents($url);
        $split_text = '<td class="table-person"><div class="person-summary"> <a class="avatar -a40" href="/';
        $followers = explode($split_text, $followers);
        array_shift($followers);
        $follower_usernames = array();
        foreach ($followers as $follower) {
            $follower = strstr($follower, '/"', true);
            array_push($follower_usernames, $follower);
        }
        $follower_string = "";
        foreach ($follower_usernames as $i) {
            $follower_string = $follower_string . "<a href='https://www.letterboxd.com/" . $i . "'>" . $i . "</a><br>";
        }

        $url = "https://www.letterboxd.com/" . $username . "/following/";
        $followings = file_get_contents($url);
        $followings = explode($split_text, $followings);
        array_shift($followings);
        $following_usernames = array();
        foreach ($followings as $following) {
            $following = strstr($following, '/"', true);
            array_push($following_usernames, $following);
        }
        $following_string = "";
        foreach ($following_usernames as $i) {
            $following_string = $following_string . "<a href='https://www.letterboxd.com/" . $i . "'>" . $i . "</a><br>";
        }

        $diff = array_diff($following_usernames, $follower_usernames);
        $diff_string = "";
        foreach ($diff as $i) {
            $diff_string = $diff_string . "<a href='https://www.letterboxd.com/" . $i . "'>" . $i . "</a><br>";
        }

        echo "<br>";
        echo "<center>";
        echo "<table>";
        echo "<tr><th><h3>Following</h3></th><th><h3>Followers</h3></th><th><h3>Not Following You Back</h3></th></tr>";
        echo "<tr><td style='padding:60px; vertical-align: top; padding-top: 0em;'>$following_string</td><td style='padding:60px; vertical-align: top; padding-top: 0em;'>$follower_string</td><td style='padding:60px;vertical-align: top; padding-top: 0em;'>$diff_string</td></tr>";
        echo "</table>";
        echo "</center>";

    }

    else if ($site == "GitHub") {
        echo "<center> <h2> CSDS 285 Project 3: GitHub Following Stats </h2> ";
        echo '<center><a href="https://www.github.com"><img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" style="width:100"></a>';
        echo "<br>";
        echo "<br>";
        echo "<br>";

        echo "<form action='p3.php' method='GET'>";
        echo "<label for='site'>Choose a site:</label>";
        echo "<select name='site' id='site'>";
        echo "<option value='GitHub'>GitHub</option>";
        echo "<option value='Twitter'>Twitter</option>";
        echo "<option value='Letterboxd'>Letterboxd</option>";
        echo "</select>";
        echo "<br><br>";

        echo "<br>";
        echo "<br>";
        echo "<center>";
        echo " <textarea cols=60 rows=1 name='username'>";
        echo "</textarea>";
        echo "<center> <input type=submit style= 'margin-top: 25px; height: 3%; width:5%'>";
        echo "</form>" ;
        echo "</center>";

        echo "<center> <h3> Username: </h3>" .$username;
        echo "<br><br>";

        # Constructing url
        $url = "https://github.com/" . $username . "?tab=followers";
        $followers = file_get_contents($url);
        $split_text = '<span class="Link--secondary';  # pl-1">';
        $followers = explode($split_text, $followers);
        array_shift($followers);
        $follower_usernames = array();

        # Creating follower string
        foreach ($followers as $follower) {
            $follower = strstr($follower, '</span>', true);
            $follower = str_replace(' pl-1">', "", $follower);
            $follower = str_replace('">', "", $follower);
            array_push($follower_usernames, $follower);
        }

        $follower_string = "";
        foreach ($follower_usernames as $i) {
            $follower_string = $follower_string . "<a href='https://www.github.com/" . $i . "'>" . $i . "</a><br>";
        }
        echo $followerstring;

        # Constructing url
        $url = "https://github.com/" . $username . "?tab=following";
        $followings = file_get_contents($url);
        $followings = explode($split_text, $followings);
        array_shift($followings);
        $following_usernames = array();

        # Creating following string
        foreach ($followings as $following) {
            $following = strstr($following, '</span>', true);
            $following = str_replace(' pl-1">', "", $following);
            $following = str_replace('">', "", $following);
            array_push($following_usernames, $following);
    }

        $following_string = "";
        foreach ($following_usernames as $i) {
            $following_string = $following_string . "<a href='https://www.github.com/" . $i . "'>" . $i . "</a><br>";
        }

        #Creating difference string
        $diff = array_diff($following_usernames, $follower_usernames);
        $diff_string = "";
        foreach ($diff as $i) {
            $diff_string = $diff_string . "<a href='https://www.github.com/" . $i . "'>" . $i . "</a><br>";
        }

        # Organzing strings into table
        echo "<br>";
        echo "<center>";
        echo "<table>";
        echo "<tr><th><h3>Following</h3></th><th><h3>Followers</h3></th><th><h3>Not Following You Back</h3></th></tr>";
        echo "<tr><td style='padding:60px; vertical-align: top; padding-top: 0em;'>$following_string</td><td style='padding:60px; vertical-align: top; padding-top: 0em;'>$follower_string</td><td style='padding:60px;vertical-align: top; padding-top: 0em;'>$diff_string</td></tr>";
        echo "</table>";
        echo "</center>";
    }

}

?>
