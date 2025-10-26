<?php

class CommentModel
{
    public int $id;
    public int $post_id;
    public int $user_id;
    public int $parent_id;
    public string $content;
    public array $files;
    public int $depth;
    public string $sort;
    public int $created_at;
    public int $updated_at;
    public AuthorModel|null $author = null;

    // 현재 코멘트의 하위 코멘트 수
    public int $comment_count = 0;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->post_id = $data['post_id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 0;
        $this->parent_id = $data['parent_id'] ?? 0;
        $this->content = $data['content'] ?? '';
        $this->files = explode(',', $data['files'] ?? '');
        $this->depth = $data['depth'] ?? 0;
        $this->sort = $data['sort'] ?? '';
        $this->created_at = $data['created_at'] ?? 0;
        $this->updated_at = $data['updated_at'] ?? 0;

        // 현재 코멘트의 하위 코멘트 수
        $this->comment_count = $data['comment_count'] ?? 0;

        // 작성자 정보가 있으면 AuthorModel 생성
        $this->author = new AuthorModel($data);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'content' => $this->content,
            'files' => $this->files,
            'depth' => $this->depth,
            'sort' => $this->sort,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'author' => $this->author ? $this->author->toArray() : null,
            'comment_count' => $this->comment_count,
        ];
    }

    public function data(): array
    {
        return $this->toArray();
    }
}
