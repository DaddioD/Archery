<?php
define('DB_FILE', __DIR__ . '/data.json');

function get_db() {
    if (!file_exists(DB_FILE)) {
        $defaultData = [
            'user' => [
                'username' => 'admin',
                'password' => 'password123',
                'team_name' => 'Panthers Archery',
                'school' => 'Central High School'
            ],
            'roster' => [],
            'meets' => []
        ];
        file_put_contents(DB_FILE, json_encode($defaultData, JSON_PRETTY_PRINT));
        return $defaultData;
    }
    
    $content = file_get_contents(DB_FILE);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function save_db($data) {
    return file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}