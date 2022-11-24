<?php

$id = $_GET['id'] ?? null;
$quality = $_GET['quality'] ?? null;
if ($id == 'douyin') {
    $firsturl = 'https://live.douyin.com/aweme/v1/web/backpack/match/list/?aid=6383&device_platform=web';
    $matchcontent = file_get_contents($firsturl);
    $arr = json_decode($matchcontent, true);
    $liveid = 0;
    foreach ($arr["match_list"] as $value) {
        if ($value["room_status"] == 2) {
            $liveid = $value["match_id"];
        }
    };
    if ($liveid == 0) {
        header("Location: https://cdn.jsdelivr.net/gh/youshandefeiyang/IPTV/telegram/playad.m3u8");
        exit();
    }
    $liveurl = 'https://live.douyin.com/fifaworldcup/' . $liveid;
    function get_roomid($url)
    {
        $headers = array(
            'referer:https://live.douyin.com/',
            'upgrade-insecure-requests: 1',
            'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    // $data = urldecode(get_roomid($liveurl));
    $data = urldecode(file_get_contents($liveurl));
    $reg = "/\"roomid\"\:\"[0-9]+\"/i";
    preg_match($reg, $data, $roomid);
    $nreg = "/[0-9]+/";
    preg_match($nreg, $roomid[0], $realid);
    $mediaurl = "https://live.douyin.com/webcast/room/info_by_scene/?aid=6383&live_id=1&device_platform=web&language=zh-CN&enter_from=web_search&cookie_enabled=true&screen_width=1920&screen_height=1080&browser_language=zh-CN&browser_name=Chrome&room_id=$realid[0]&scene=pc_stream_4k";
    $flvcontent = file_get_contents($mediaurl);
    $narr = json_decode($flvcontent, true);
    $playarr = $narr["data"]["stream_url"]["live_core_sdk_data"]["pull_data"]["Flv"];
    switch ($quality) {
        case "hd":
            $realquality = "hd";
            break;
        case "sd":
            $realquality = "sd";
            break;
        case "ld":
            $realquality = "ld";
            break;
        default:
            $realquality = "uhd";
    }
    foreach ($playarr as $qualityvalue) {
        if (in_array($realquality, $qualityvalue)) {
            $playurl = $qualityvalue["url"];
        }
    };
    header("Location: $playurl");
} else {
    $arr = array('msg' => "failed", 'data' => "wrong value");
    echo json_encode($arr, 320);
    exit();
}