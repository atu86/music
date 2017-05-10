 <?php
function curl_get($url)
{
    $refer = "http://music.163.com/";
    $header[] = "Cookie: " . "appver=1.5.0.75771;";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $refer);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
 
function music_search($word, $type)
{
    $url = "http://music.163.com/api/search/pc";
    $post_data = array(
        's' => $word,
        'offset' => '0',
        'limit' => '20',
        'type' => $type,
    );
    $referrer = "http://music.163.com/";
    $URL_Info = parse_url($url);
    //$values = [];
    $result = '';
    $request = '';
    foreach ($post_data as $key => $value) {
        $values[] = "$key=" . urlencode($value);
    }
    $data_string = implode("&", $values);
    if (!isset($URL_Info["port"])) {
        $URL_Info["port"] = 80;
    }
    $request .= "POST " . $URL_Info["path"] . " HTTP/1.1\n";
    $request .= "Host: " . $URL_Info["host"] . "\n";
    $request .= "Referer: $referrer\n";
    $request .= "Content-type: application/x-www-form-urlencoded\n";
    $request .= "Content-length: " . strlen($data_string) . "\n";
    $request .= "Connection: close\n";
    $request .= "Cookie: " . "appver=1.5.0.75771;\n";
    $request .= "\n";
    $request .= $data_string . "\n";
    $fp = fsockopen($URL_Info["host"], $URL_Info["port"]);
    fputs($fp, $request);
    $i = 1;
    while (!feof($fp)) {
        if ($i >= 15) {
            $result .= fgets($fp);
        } else {
            fgets($fp);
            $i++;
        }
    }
    fclose($fp);
    return $result;
}

function get_music_info($music_id)
{
    $url = "http://music.163.com/api/song/detail/?id=" . $music_id . "&ids=%5B" . $music_id . "%5D";
    return curl_get($url);
}

function get_newsong_info(){
    $url = "http://music.163.com/api/playlist/detail?id=3779629";
    return curl_get($url);
}

//搜索歌曲
if(isset($_GET['key'])){
$key = $_GET['key'];
$res=music_search($key,1);
$res=json_decode($res);
$res=$res->result->songs;
$idarr=array();
foreach($res as $id){
    array_push($idarr,$id->id);
}

    $songinfo = array();

    foreach($idarr as $id){
       $musicinfo = get_music_info($id);
        $mp3url   = array_pop(json_decode($musicinfo)->songs)->mp3Url;
        $songname = array_pop(json_decode($musicinfo)->songs)->name;
        $songpic  = array_pop(json_decode($musicinfo)->songs)->album->picUrl;
        $onesong  = array();
        $onesong['name'] = $songname;
        $onesong['songpic'] = $songpic;
        $onesong['mp3url'] = $mp3url;

        array_push($songinfo,$onesong);


    }
    var_dump($songinfo);
}


//新歌榜
if($_GET['bd']=1){
    $res=get_newsong_info();
    echo "<pre>";
    var_dump($res);
}
