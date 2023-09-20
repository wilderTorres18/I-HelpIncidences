<?php

function translations($json) {
    if(!file_exists($json)) {
        return [];
    }
    return json_decode(file_get_contents($json), true);
}

if (!function_exists('static_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function static_asset($path, $secure = null) {
        return app('url')->asset('public/' . $path, $secure);
    }
}
