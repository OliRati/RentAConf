<?php
function dg( $data ) {
    echo '<pre style="background-color: black; color: white">';
    var_dump( $data );
    echo "</pre>";
}

function dd($data) {
    dg( $data );
    die();    
}
?>
