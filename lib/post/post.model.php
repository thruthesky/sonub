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


    /**
     * Comments
     * @var CommentModel[] $comments Array of CommentModel objects
     */
    public array $comments;
    public int $created_at;
    public int $updated_at;




    public AuthorModel|null $author = null;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 0;
        $this->category = $data['category'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->content = $data['content'] ?? '';
        $this->visibility = $data['visibility'] ?? '';
        $this->files = explode(',', $data['files'] ?? '');
        $this->comments = $data['comments'] ?? [];
        $this->created_at = $data['created_at'] ?? 0;
        $this->updated_at = $data['updated_at'] ?? 0;


        // 작성자 정보가 있으면 AuthorModel 생성
        $this->author = new AuthorModel($data);
    }

    /**
     * PostModel 객체를 배열로 변환
     * API 응답을 위해 필수 메서드
     *
     * @return array 게시물 정보 배열
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category' => $this->category,
            'title' => $this->title,
            'content' => $this->content,
            'visibility' => $this->visibility,
            'files' => $this->files,
            'comments' => $this->comments,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'author' => $this->author ? $this->author->toArray() : null,
        ];
        return $data;
    }

    public function data(): array
    {
        return $this->toArray();
    }
}
