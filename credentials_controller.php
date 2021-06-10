
<?php

require_once ('constants.php');

/**
 * Will return the correct DB credentials depending on the database name.
 * @param string $databaseName
 * @return array|null [FILE, HOST, USER, PASSWORD] or null
 */
function getDBCredentials(string $databaseName): ?array
{

    # check to make sure the given db name is valid
    if (!in_array($databaseName, kDATABASES)) {
        return null;
    }

    $ini = parse_ini_file('app.ini.php');

    return array($ini[$databaseName.'_file'], $ini[$databaseName.'_host'],
        $ini[$databaseName.'_user'], $ini[$databaseName.'_pass']);
}