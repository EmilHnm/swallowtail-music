<?php

function file_path_helper($id)
{
    if ($id >= 1000000000) {
        $parts = str_split(strrev($id), 3);
        $parts = array_reverse($parts);

        $part = implode('/', $parts);
        return "/$part";
    }

    return null;
}
