<?php

class FeedEntryModel
{
    public int $id;
    public int $receiver_id;
    public int $post_id;
    public int $post_author_id;
    public int $created_at;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->receiver_id = $data['receiver_id'] ?? 0;
        $this->post_id = $data['post_id'] ?? 0;
        $this->post_author_id = $data['post_author_id'] ?? 0;
        $this->created_at = $data['created_at'] ?? 0;
    }
}
