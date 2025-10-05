<?php

class UserModel
{
    public int $id;
    public string $firebase_uid;
    public string $display_name;
    public string $created_at;
    public string $updated_at;
    public int $birthday;
    public string $gender;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->firebase_uid = $data['firebase_uid'] ?? '';
        $this->display_name = $data['display_name'] ?? '';
        $this->created_at = $data['created_at'] ?? '';
        $this->updated_at = $data['updated_at'] ?? '';
        $this->birthday = $data['birthday'] ?? 0;
        $this->gender = $data['gender'] ?? '';
    }
}
