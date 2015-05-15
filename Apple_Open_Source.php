<?php
    function get_open_source_files($open_source_URL, $sub) {
        $is_sub = false;
        if (substr($sub, strlen($sub) - 1, 1) == '/') {
            if (!file_exists($sub)) {
                mkdir($sub, 0775, true);
            }
            $is_sub = true;
        }
        $content = file_get_contents($open_source_URL);
        if ($is_sub) {
            preg_match_all('/><\/a><\/td><td><a href="(.*?)">(.*?)<\/a>/', $content, $matches);
            $URLs = $matches[1];
            $description = $matches[2];
            for ($i = 0; $i < count($URLs); $i++) {
                if ($description[$i] == 'Parent Directory') {
                    continue;
                }
                $next = $open_source_URL.$URLs[$i];
                if (substr($next, strlen($next) - 1, 1) != '/') {
                    $next .= '?txt';
                }
                get_open_source_files($next, $sub.$URLs[$i]);
            }  
        } else {
            $fd = fopen($sub, 'w');
            if ($fd) {
                fwrite($fd, $content);
                print $sub." saved!\n";
            } else {
                print $sub." failed!\n";
            }
            fclose($fd);
        }
    }
    print "Apple Open Source URL: ";
    $open_source_URL = trim(fgets(STDIN));
    if (strpos($open_source_URL, 'http://www.opensource.apple.com/source/') === 0) {
        $paths = preg_split('/\//', $open_source_URL);
        $start = end($paths);
        if ($start == '') {
            $start = $paths[count($paths) - 2].'/';
        }
        get_open_source_files($open_source_URL, $start);   
    } else {
        print "Accepts URL starting with http://www.opensource.apple.com/source/ only.\n";
    }
?>