<?php

if (! function_exists('bytesToHuman')) {
    function bytesToHuman($bytes)
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (! function_exists('mode')) {
    /**
     * checking run mode.
     *
     * @param string $modeToLookFor
     * @return bool
     */
    function mode(string $modeToLookFor): bool
    {
        $runMode = \Illuminate\Support\Str::lower(config('fileproxy.mode'));

        if ($runMode === 'default') {
            return true;
        }

        $modeToLookFor = \Illuminate\Support\Str::lower(trim($modeToLookFor));

        return $modeToLookFor === $runMode;
    }
}
