<?php

class PostModel
{
    public int $id;
    public int $user_id;
    public string $category;
    public string $title;
    public string $content;
    public string $visibility;
    public array $files;
    public int $created_at;
    public int $updated_at;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 0;
        $this->category = $data['category'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->content = $data['content'] ?? '';
        $this->visibility = $data['visibility'] ?? '';
        $this->files = explode(',', $data['files'] ?? '');
        $this->created_at = $data['created_at'] ?? 0;
        $this->updated_at = $data['updated_at'] ?? 0;
    }

    /**
     * PostModel 객체를 배열로 변환
     * API 응답을 위해 필수 메서드
     *
     * @return array 게시물 정보 배열
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category' => $this->category,
            'title' => $this->title,
            'content' => $this->content,
            'visibility' => $this->visibility,
            'files' => $this->files,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function data(): array
    {
        return $this->toArray();
    }
}
