<?php
    if (isset($_POST["minutely"])) {
        $file = fopen("config.txt", "w");
        fwrite($file, "minutely");
        fclose($file);
    }
    if (isset($_POST["daily"])) {
        $file = fopen("config.txt", "w");
        fwrite($file, "daily");
        fclose($file);
    }
    elseif (isset($_POST["weekly"])) {
        $file = fopen("config.txt", "w");
        fwrite($file, "weekly");
        fclose($file);
    }
    elseif (isset($_POST["monthly"])) {
        $file = fopen("./config.txt", "w");
        fwrite($file, "monthly");
        fclose($file);
    }
    header("Location: index.php");
?>