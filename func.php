<?php
function h(string $str){
    return htmlspecialchars($str,ENT_QUOTES);
}