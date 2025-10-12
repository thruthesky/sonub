<?php

/**
 * PostListModel Class
 *
 * Model class for post list query results.
 * Contains an array of PostModel objects with pagination information.
 *
 * @package Sonub\Post
 */
class PostListModel
{
    /**
     * @var PostModel[] Array of post objects
     */
    public array $posts;

    /**
     * @var int Total number of posts
     */
    public int $total;

    /**
     * @var int Current page number (starts from 1)
     */
    public int $page;

    /**
     * @var int Number of posts per page
     */
    public int $per_page;

    /**
     * @var int Total number of pages
     */
    public int $total_pages;

    /**
     * PostListModel Constructor
     *
     * @param PostModel[] $posts Array of post objects
     * @param int $total Total number of posts
     * @param int $page Current page number (default: 1)
     * @param int $per_page Number of posts per page (default: 20)
     */
    public function __construct(
        array $posts,
        int $total,
        int $page = 1,
        int $per_page = 20
    ) {
        $this->posts = $posts;
        $this->total = $total;
        $this->page = $page;
        $this->per_page = $per_page;

        // Calculate total pages
        $this->total_pages = (int)ceil($total / $per_page);
    }

    /**
     * Convert PostListModel object to array
     *
     * Used for API responses, includes pagination info and post list.
     *
     * @return array Post list information array
     */
    public function toArray(): array
    {
        return [
            'posts' => array_map(fn($post) => $post->toArray(), $this->posts),
            'pagination' => [
                'total' => $this->total,
                'page' => $this->page,
                'per_page' => $this->per_page,
                'total_pages' => $this->total_pages,
            ]
        ];
    }

    /**
     * Alias for toArray() method
     *
     * @return array Post list information array
     */
    public function data(): array
    {
        return $this->toArray();
    }

    /**
     * Check if post list is empty
     *
     * @return bool True if no posts, false otherwise
     */
    public function isEmpty(): bool
    {
        return empty($this->posts);
    }

    /**
     * Get number of posts
     *
     * @return int Number of posts on current page
     */
    public function count(): int
    {
        return count($this->posts);
    }

    /**
     * Check if next page exists
     *
     * @return bool True if next page exists, false otherwise
     */
    public function hasNextPage(): bool
    {
        return $this->page < $this->total_pages;
    }

    /**
     * Check if previous page exists
     *
     * @return bool True if previous page exists, false otherwise
     */
    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }

    /**
     * Get next page number
     *
     * @return int|null Next page number (null if no next page)
     */
    public function getNextPage(): ?int
    {
        return $this->hasNextPage() ? $this->page + 1 : null;
    }

    /**
     * Get previous page number
     *
     * @return int|null Previous page number (null if no previous page)
     */
    public function getPreviousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->page - 1 : null;
    }
}
