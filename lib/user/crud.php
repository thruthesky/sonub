<?php
function user_record_create(array $input): int
{
    global $db;
    $fields = [];
    $values = [];
    foreach ($input as $k => $v) {
        $fields[] = $k;
        $values[] = "'" . $db->real_escape_string($v) . "'";
    }
    $sql = "INSERT INTO `user` (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $values) . ")";
    if (!$db->query($sql)) {
        throw new Exception("Database error: " . $db->error);
    }
    return (int)$db->insert_id;
}
