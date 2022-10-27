<?php

return [
    /*
     * This host will be used if none is specified
     * when executing the `remote` command.
     */
    'default_host' => 'default',

    /*
    * When set to true, A confirmation prompt will be shown before executing the `remote` command.
    */
    'needs_confirmation' => env('REMOTE_NEEDS_CONFIRMATION', false),
];
