<?php
include './Result.php';
include './ResultList.php';
include './PageScrapper.php';
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        
        <?php
        
        $scraper = new PageScrapper();
        $url="https://www.black-ink.org";
        $categorey="Digitalia";
        $result_list = $scraper->scrap_page($url, $categorey);
        $json = $result_list->getJSON();
        echo $json;
        
        ?>

    </body>
</html>
