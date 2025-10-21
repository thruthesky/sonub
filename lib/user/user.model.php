<?php

class UserModel
{
    public int $id;
    public string $firebase_uid;
    /**
     * @deprecated use first_name
     */
    public string $display_name;
    public string $first_name;
    public string $last_name;
    public string $middle_name;
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
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->middle_name = $data['middle_name'] ?? '';
        $this->created_at = $data['created_at'] ?? '';
        $this->updated_at = $data['updated_at'] ?? '';
        $this->birthday = $data['birthday'] ?? 0;
        $this->gender = $data['gender'] ?? '';
        $this->photo_url = $data['photo_url'] ?? '';
        $this->display_name = $this->first_name;
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
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

    public function displayDisplayName(): string
    {
        return htmlspecialchars($this->display_name ? $this->display_name : 'No name', ENT_QUOTES, 'UTF-8');
    }

    /**
     * 이름(First Name) 표시용 문자열 반환
     * HTML 이스케이프 처리됨
     *
     * @return string 이름 또는 빈 문자열
     */
    public function displayFirstName(): string
    {
        return htmlspecialchars($this->first_name ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * 성(Last Name) 표시용 문자열 반환
     * HTML 이스케이프 처리됨
     *
     * @return string 성 또는 빈 문자열
     */
    public function displayLastName(): string
    {
        return htmlspecialchars($this->last_name ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * 중간 이름(Middle Name) 표시용 문자열 반환
     * HTML 이스케이프 처리됨
     *
     * @return string 중간 이름 또는 빈 문자열
     */
    public function displayMiddleName(): string
    {
        return htmlspecialchars($this->middle_name ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * 전체 이름 표시용 문자열 반환
     * First Name + Middle Name + Last Name 순서로 조합
     *
     * @return string 전체 이름
     */
    public function displayFullName(): string
    {
        $parts = [];

        if (!empty($this->first_name)) {
            $parts[] = $this->displayFirstName();
        }

        if (!empty($this->middle_name)) {
            $parts[] = $this->displayMiddleName();
        }

        if (!empty($this->last_name)) {
            $parts[] = $this->displayLastName();
        }

        return implode(' ', $parts);
    }
}
