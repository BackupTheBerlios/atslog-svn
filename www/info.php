<?php

phpinfo();

$headers = apache_request_headers();

foreach ($headers as $header => $value) {
    echo "$header: $value <br />\n";
}

echo "<br><br><br>";
$headers = getallheaders();

foreach ($headers as $header => $value) {
    echo "$header: $value <br />\n";
}

?> 