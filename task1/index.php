<?php
//#5 спарсить базу данных серила lostfilm
echo "<br><hr>\n";
$serials = file_get_contents("https://www.lostfilm.tv/serials.php");
$serials = mb_convert_encoding($serials, "UTF-8", "windows-1251");
$expr = '|<a href="/browse.php\?cat=(\d{1,3})" class="bb_a">(.*)<br><span>.*</span></a>|i';

$matches = [];

if (preg_match_all($expr, $serials, $matches)) {
    unset($serials);                        //на всякий случай удаляем главную страницу

    foreach ($matches[1] as $value) {
        sleep (1);
        $url = "https://www.lostfilm.tv/browse.php?cat=".$value;
        $serial = file_get_contents($url);
        $serial = mb_convert_encoding($serial, "UTF-8", "windows-1251");
        $serialImg = [];
        $about_serial = [];
        $serialInfo = [];
        $exprImg = '|<img src="/Static/posters/(\S*)".*/>|i';
        $exprAbout = '/О сериале "(.*)"<\/h2><\/div>\s*<\/div>\s*<span>([\s\S]*?)(<br><br>|<\/span>)/i';

        preg_match($exprImg, $serial, $serialImg);
        preg_match($exprAbout, $serial, $about_serial);
        copy ("https://www.lostfilm.tv/Static/posters/".$serialImg[1], "posters/$value.jpeg");

        var_dump($serialImg);
        var_dump($about_serial);

        $serialInfo = ["serialNumber" => "$value",
            "Title" => $about_serial[1],
            "About" => htmlspecialchars($about_serial[2]),
            "img" => "/posters/$value.jpeg"];
        $serialInfo = json_encode($serialInfo, JSON_UNESCAPED_UNICODE);

        $db = fopen("serials.db", "a+");
        fwrite($db, $serialInfo);
        fclose($db);
        set_time_limit(10);
    }
}

?>






















