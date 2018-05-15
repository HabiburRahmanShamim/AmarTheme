<?php
function formatPrice($price){
    $p = explode('.', $price);
    $pre = $p[0];
    $post = '00';
    if(!empty($p['1'][0])) $post[0] = $p[1][0];
    if(!empty($p['1'][1])) $post[1] = $p[1][1];
    echo "<span class='align-bottom'>$ </span><span class='align-bottom'>$pre</span><span class='align-center' style='font-size: 18px;'>.$post</span>";
}

function cutArray($ar, $size){
    $chunks = [];
    foreach (array_chunk($ar, 3) as $p){
        $chunks[] = ['products' => $p];
    }
    return $chunks;
}

?>