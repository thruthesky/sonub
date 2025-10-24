<?php
class AuthorModel
{
    public ?string $first_name = null;
    public ?string $photo_url = null;
    public ?string $firebase_uid = null;

    public function __construct(array $data)
    {
        $this->first_name = $data['first_name'] ?? null;
        $this->photo_url = $data['photo_url'] ?? null;
        $this->firebase_uid = $data['firebase_uid'] ?? null;

        //
        unset($data['first_name'], $data['photo_url'], $data['firebase_uid']);
    }

    /**
     * AuthorModel 객체를 배열로 변환
     *
     * @return array 작성자 정보 배열
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'photo_url' => $this->photo_url,
            'firebase_uid' => $this->firebase_uid,
        ];
    }
}
