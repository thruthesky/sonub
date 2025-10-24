<?php

class CommentModel
{
    public int $id;
    public int $user_id;
    public string $content;
    public array $files;
    public int $created_at;
    public int $updated_at;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 0;
        $this->content = $data['content'] ?? '';
        $this->files = explode(',', $data['files'] ?? '');
        $this->created_at = $data['created_at'] ?? 0;
        $this->updated_at = $data['updated_at'] ?? 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'content' => $this->content,
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
