<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . DATABASE_SERVER . ';dbname=' . DATABASE_NAME,
    'username' => DB_USER,
    'password' => DB_PASS,
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
