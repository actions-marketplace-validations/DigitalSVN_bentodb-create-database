<?php
$db_host = getenv('DB_HOST');
$db_port = getenv('DB_PORT');
$db_name = getenv('DB_NAME');
$db_username = getenv('DB_USERNAME');
$db_password = getenv('DB_PASSWORD');

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s',
    $db_host,
    $db_port,
    $db_name
);
$options = [
    PDO::ATTR_TIMEOUT => 3, // in seconds
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_SSL_CA => true,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];
$pdo = new PDO($dsn, $db_username, $db_password, $options);

/**
 * Create table
 */
$output = "Creating table...\n";
$create_statement = 'CREATE TABLE IF NOT EXISTS `books` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255),
    `author` varchar(255),
    PRIMARY KEY (`id`)
);';
$create_result = $pdo->exec($create_statement);
$output .= $create_statement . "\n";
$output .= "Table created.\n";

/**
 * Describe table
 */
$describe_statement = 'DESCRIBE `books`;';
$describe_result = $pdo->query($describe_statement);
$output .= $describe_statement . "\n";
$output .= arrayToTextTable($describe_result->fetchAll(PDO::FETCH_ASSOC));

/**
 * Insert data into table
 */
$output .= "Inserting data...\n";
$insert_statement = "INSERT INTO `books` (`title`, `author`) VALUES 
      ('The Da Vinci Code', 'Dan Brown'),
      ('Harry Potter and the Chamber of Secrets', 'J. K. Rowling');";
$pdo->exec($insert_statement);
$output .= $insert_statement . "\n";
$output .= "Data inserted.\n";

/**
 * Select data from table
 */
$select_statement = "SELECT * FROM `books` ORDER BY title ASC;";
$select_result = $pdo->query($select_statement);
$output .= $select_statement . "\n";
$output .= arrayToTextTable($select_result->fetchAll(PDO::FETCH_ASSOC));

echo $output;


/**
 * Output a neatly structured text table
 *
 * @param array $array
 * @return string
 */
function arrayToTextTable(array $array) {
    $keys = array_keys(reset($array));
    $widths = array_map('strlen', $keys);
    foreach ($array as $row) {
        $widths = array_map('max', $widths, array_map('callbackStrlen', $row));
    }
    $format = '| ' . implode(' | ', array_map(function ($w) { return "%-{$w}s"; }, $widths)) . " |\n";
    $sep = '+-' . implode('-+-', array_map(function ($w) { return str_repeat('-', $w); }, $widths)) . "-+\n";
    $output = $sep;
    $output .= vsprintf($format, $keys);
    $output .= $sep;
    foreach ($array as $row) {
        $output .= vsprintf($format, $row);
    }
    $output .= $sep;
    return $output;
}

/**
 * Callback function for array_map
 * Handles null values (strlen does not)
 *
 * @param $value
 * @return int
 */
function callbackStrlen($value)
{
    return is_null($value) ? 0 : strlen($value);
}