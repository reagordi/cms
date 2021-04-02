<?php

$collector->get('test', function(){
    return api_ok( null, array(
        'files' => get_included_files()
    ));
});
