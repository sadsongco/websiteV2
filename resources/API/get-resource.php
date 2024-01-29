<?php

function sortResourcesByName($a, $b) {
    if ($a['resource'] == $b['resource']) {
        return 0;
    }
    return ($a['resource'] < $b['resource']) ? -1 : 1;
}

function getSouncloudPlaylists($path) {
    $file_string = file_get_contents("../".$path."soundcloud_playlists.txt");
    $playlist_arr = explode("\n", $file_string);
    $playlists = [];
    foreach ($playlist_arr as $playlist) {
        if ($playlist == "") continue;
        $playlist_data = explode("|", $playlist);
        $playlists[] = [
            "playlist_title"=>$playlist_data[0],
            "playlist_id"=>$playlist_data[1],
            "secret_token"=>$playlist_data[2]
        ];
    }
    return $playlists;
}

function getResource($section) {
    $output = [];
    $sub_dir = '';
    
    if ($section == 'press_shots') $sub_dir = 'full_res/';
    
    $path = 'resource_dirs/'.$section.'/';
    $output['name'] = ucwords(str_replace("_", " ", $section));
    $output['resources'] = [];
    
    try {
        if ($section == 'playlists') {
            $output['resources'] = ['playlists'=>getSouncloudPlaylists($path)];
            return $output;
        }
        if ($handle = opendir('../'.$path.$sub_dir)) {
            while (false != ($entry = readdir($handle))) {
                if (substr($entry, 0, 1) == '.') continue;
                $resource = ["path"=>'/resources/'.$path, "resource"=>$entry];
                if ($section == 'press_shots') {
                    $resource['full_res_size'] = getimagesize("../".$path."full_res/".$entry);
                    $resource['web_size'] = getimagesize("../".$path."web/".$entry);
                    $resource['thumbnail_size'] = getimagesize("../".$path."thumbnail/".$entry);
                    $resource['img_preview'] = true;
                    $resource['photo_credit'] = "Scarlet Page <a href = 'https://www.instagram.com/scarletpage/' target='_blank'>@scarletpage</a>";
                }
                if ($section == 'logos') {
                    $resource['logo_size'] = getimagesize("../".$path.$entry);
                    $resource['logo_preview'] = true;
                }
                $output['resources'][] = $resource;
            }
            closedir($handle);
        }
    
    } catch (Exception $e) {
        $output['success'] = false;
        $output['error'] = $e->getMessage();
    }

    usort($output['resources'], 'sortResourcesByName');
    
    return $output;
}