<?php
header("Content-type: text/css");
foreach (scandir(".") as $file)
    if (str_ends_with($file, ".min.css"))
        echo file_get_contents($file);
