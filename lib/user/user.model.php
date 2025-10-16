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
    public string $photo_url;


    public bool $is_admin = false;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->firebase_uid = $data['firebase_uid'] ?? '';
        $this->display_name = $data['display_name'] ?? '';
        $this->created_at = $data['created_at'] ?? '';
        $this->updated_at = $data['updated_at'] ?? '';
        $this->birthday = $data['birthday'] ?? 0;
        $this->gender = $data['gender'] ?? '';
        $this->photo_url = $data['photo_url'] ?? '';
    }

    /**
     * UserModel 객체를 배열로 변환
     *
     * @return array 사용자 정보 배열
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firebase_uid' => $this->firebase_uid,
            'display_name' => $this->display_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'birthday' => $this->birthday,
            'gender' => $this->gender,
            'photo_url' => $this->photo_url,
            'is_admin' => $this->is_admin,
        ];
    }

    public function data(): array
    {
        return $this->toArray();
    }
}
