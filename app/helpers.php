<?php

function responseHelper($data){
    return response()->json(
        [array_key_first($data) => $data[array_key_first($data)]], $data['status_code']);
}
