<?php
/**
 * Created by Aleksandar Ardjanliev.
 * Date: 7/10/2019
 * Time: 1:30 PM
 */

//session_start();
//session_regenerate_id();
require "sessionClassDB.php";
require_once ("config.inc.php");



try{
    function connectPDO ()
    {
        $con = new PDO("mysql:host=" . DBHOST . ";dbname=" . DB, DBUSER, DBPASS);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if($con){
            return $con;
        }else{
            throw new PDOException("There is no connection to database at the moment.");
        }
    }
}catch(PDOException $e){
    errorMessage($e);
}

$con = connectPDO();
$session = new sessionClassDB($con);

function connectMysqli(){
    $con = new mysqli(DBHOST, DBUSER, DBPASS, DB);
    return $con;
}


//function to create token...
function token($length = 64)
{
    $token = bin2hex(random_bytes($length));
    $_SESSION['token'] = $token;
    return $token;
}


//function to shorten text to given max characters...
function substrwords($text, $maxchar, $end = '...')
{
    if (strlen($text) > $maxchar) {
        $words = preg_split('/\s/', $text);
        $output = '';
        $i = 0;
        while (1) {
            $length = strlen($output) + strlen($words[$i]);
            if ($length > $maxchar) {
                break;
            } else {
                $output .= " " . $words[$i];
                ++$i;
            }
        }
        $output .= $end;
    } else {
        $output = $text;
    }
    return $output;
}

//Timestamp to date...
function timestampToDate($timestamp, $format = "Y-m-d")
{
    if($timestamp == "" || $timestamp == null){
        return false;
    }else{
        return date($format, strtotime($timestamp));
    }

}

function reformatTextToTitles($text, $length = 20){
    $textArray = explode("\n", $text);
    $newText = array();
    foreach ($textArray as $row){
        $row = trim($row);
        $newText[] = preg_replace("/^(?!(www|http|-|•|\d|--||<a|<span|<li|<|@|\*)).{1,$length}$/","<span class='font-weight-bold' style='font-size: 130%'>$row</span>", $row);
    }
    return implode("\n", $newText);
}

function reformatTextToBold($text, $length = 50){
    $textArray = explode("\n", $text);
    $newText = array();
    foreach ($textArray as $row){
        $row = trim($row);
        $newText[] = preg_replace("/^(@).{1,$length}$/","<b style='font-size: 120%'>" . substr($row, 1) . "</b>", $row);
    }
    return implode("\n", $newText);
}

function getTitleFromFirstWord($text){
    $textArray = explode("\n", $text);
    //return replaceFirstPartOfString(current($textArray), "@", "");
    return strip_tags($textArray[0]);
}

function reformatTextToListing($text, $length = 100){
    $textArray = explode("\n", $text);
    $newText = array();
    /*foreach ($textArray as $row){
        $row = trim($row);
        $newText[] = preg_replace("/^(-|•|\d\)|--|).{1,$length}$/","<span class='font-italic ml-2'>$row</span>", $row);
    }
    return implode("\n", $newText);*/

    $tempList = 0;
    $listing = "";
    foreach($textArray as $row){
        if(preg_match("/^(-|•|\d\)|--|).{1,$length}$/", $row)){
            $row = trim($row);
            if($tempList == 0) $listing = "<ul>";
            $listing .= "<li class='text-danger'>$row</li>";
            $tempList = 1;
        }else{
            if($tempList == 1){
                $listing .= "</ul>";
                $newText[] = $listing;
            }
            $tempList = 0;
            $newText[] = $row;
        }
    }
    return implode("\n", $newText);
}

function reformatTextToHyperlink($text){
    $textArray = explode("\n", $text);
    $newText = array();
    foreach ($textArray as $row){
        $row = trim($row);
        $newText[] = preg_replace("/^(www|http).+$/","<a href='$row'>$row</a>", $row);
    }
    return implode("\n", $newText);
}


/** Function to format text
 * formats to heading (make sentence bold if it has first charackter "@"
 * formats listings (creates unordered lists of sentences that have "-,•,--," first charackter
 * formats url to hyperlinks
 */
//First we create array of rows (divided by new line \n) from the text
function formatText ($text){
    $textArray = explode("\n", $text);
    $textArray[] = ""; //Fix for listing if text finishes as list by adding empty key to the array at the end
    $newText = array();
    $tempList = 0; //temporary variable that will contain rows with lists in HTML format
    $listing = ""; //it is used to switch between lists and other text
    foreach ($textArray as $row){
        $row = trim($row);// \n char is trimmed here
        if(preg_match("/^(@).*$/", $row)){ //if first char is @, makes the row as heading...
            if($tempList == 1){ //if there is open listing (exp. <ul><li>.....</li>), close the listing with "</ul>"
                $listing .= "</ul>";
                $newText[] = $listing;
            }
            $newText[] = "<h6>" . substr($row, 1) . "</h6>"; //can be other heading formatting, substr is used to cut out the first char
            $tempList = 0; // Close the listing if it was open
        }elseif(preg_match("/^(-|•|--|).*$/", $row)){ //if first char is - , • , -- or , makes the row an unordered list
            if($tempList == 0) $listing = "<ul>"; //if there wasn't any list row before, then we put "<ul>" tag
            $row = str_replace("•", "", $row); //cutting out special character
            //$row = str_replace("-", "", $row); //will replace all "-" in the string, not only first one!!! NOT GOOD FOR urls
            //keep in mind that bullets in text (string) are encoded with more more characters (exp. &#8259;), so the best solution
            //is to replace/remove them with preg_replace()...

            $row = replaceFirstPartOfString($row, "-", "");
            //$row = replaceFirstPartOfString($row, "•", "");

            $listing .= "<li class='text-left'> " . trim($row) . " </li>"; //Spaces are important for hyperlink recognition (next foreach)
            $tempList = 1;
        }else{
            if($tempList == 1){ //Same as in the first block
                $listing .= "</ul>";
                $newText[] = $listing;
            }

            $tempList = 0;
            $newText[] = "<p> $row </p>"; //Spaces are important for hyperlink recognition (next foreach)
            //The rest of the rows will be considered as paragraphs
        }

    }

    //Using the formatted array $newText to find words within its elements that are URL and convert them
    // to hyperlinks
    $lastText = array();
    foreach($newText as $row){
        $rowArray = array();
        $words = explode(" ", $row);
        foreach ($words as $word){
            if(preg_match("/^(www|http).*$/", $word)){
                $word = str_replace("(", "", $word);
                $word = str_replace(")", "", $word);
                $word = str_replace(",", "", $word);
                //if there are brackets or comma next to the URL we need to remove it, if just placing space
                //between it and the URL, they will still be considered as part of the URL together with the given space

                $word = "<a style='color:#ffa; word-wrap:break-word' class='font-italic' href='" . $word . "'>" . $word . "</a>";
                $rowArray[] = $word;
            }else{
                $rowArray[] = $word;
            }
        }
        $row = implode(" ", $rowArray);
        $lastText[] = $row;
    }

    return trim(implode("\n", $lastText));
}

function replaceFirstPartOfString($haystack, $needle, $replace){

    $pos = strpos($haystack, $needle);
    if ($pos !== false) {
        return substr_replace($haystack, $replace, 0, strlen($needle));
    }else{
        return $haystack;
    }

}