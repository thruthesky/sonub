<?php

/**
 * Post Entity Class
 *
 * Represents a post/article record in the database
 *
 * Usage:
 * $post = Post::get(1);                           // Get post by ID
 * $post = Post::create(['title' => 'My Post', 'content' => 'Content...', 'user_id' => 1]);
 * $post->update(['title' => 'Updated Title']);    // Update post
 * $post->delete();                                // Delete post
 *
 * $posts = Post::find(['status' => 'published']); // Find published posts
 * $posts = Post::getByUser(1);                    // Get posts by user
 */

require_once __DIR__ . '/entity.php';

class Post extends Entity {
    /**
     * Database table name
     * @var string
     */
    protected static $table = 'posts';

    /**
     * Get posts by user ID
     * @param int $userId User ID
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of Post entities
     */
    public static function getByUser($userId, $limit = null, $offset = 0) {
        return static::find(['user_id' => $userId], $limit, $offset);
    }

    /**
     * Get published posts
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of Post entities
     */
    public static function getPublished($limit = null, $offset = 0) {
        return static::find(['status' => 'published'], $limit, $offset);
    }

    /**
     * Get draft posts
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of Post entities
     */
    public static function getDrafts($limit = null, $offset = 0) {
        return static::find(['status' => 'draft'], $limit, $offset);
    }

    /**
     * Get post title
     * @return string|null Post title
     */
    public function getTitle() {
        return $this->getValue('title');
    }

    /**
     * Get post content
     * @return string|null Post content
     */
    public function getContent() {
        return $this->getValue('content');
    }

    /**
     * Get post excerpt (first N characters of content)
     * @param int $length Length of excerpt
     * @return string Post excerpt
     */
    public function getExcerpt($length = 200) {
        $content = strip_tags($this->getContent());
        if (strlen($content) > $length) {
            return substr($content, 0, $length) . '...';
        }
        return $content;
    }

    /**
     * Get post status
     * @return string|null Post status
     */
    public function getStatus() {
        return $this->getValue('status', 'draft');
    }

    /**
     * Get post author ID
     * @return int|null User ID
     */
    public function getUserId() {
        return $this->getValue('user_id');
    }

    /**
     * Get post author
     * @return User|null User entity or null
     */
    public function getAuthor() {
        $userId = $this->getUserId();
        if ($userId && class_exists('User')) {
            require_once __DIR__ . '/user.php';
            return User::get($userId);
        }
        return null;
    }

    /**
     * Get post slug
     * @return string|null Post slug
     */
    public function getSlug() {
        return $this->getValue('slug');
    }

    /**
     * Get view count
     * @return int View count
     */
    public function getViewCount() {
        return (int) $this->getValue('view_count', 0);
    }

    /**
     * Check if post is published
     * @return bool True if published
     */
    public function isPublished() {
        return $this->getStatus() === 'published';
    }

    /**
     * Check if post is draft
     * @return bool True if draft
     */
    public function isDraft() {
        return $this->getStatus() === 'draft';
    }

    /**
     * Publish the post
     * @return bool True if successful
     */
    public function publish() {
        $data = [
            'status' => 'published',
            'published_at' => date('Y-m-d H:i:s')
        ];
        return $this->update($data);
    }

    /**
     * Unpublish the post (set as draft)
     * @return bool True if successful
     */
    public function unpublish() {
        return $this->update(['status' => 'draft']);
    }

    /**
     * Increment view count
     * @return bool True if successful
     */
    public function incrementViewCount() {
        $currentCount = $this->getViewCount();
        return $this->update(['view_count' => $currentCount + 1]);
    }

    /**
     * Generate slug from title
     * @param string $title Post title
     * @return string Generated slug
     */
    protected static function generateSlug($title) {
        // Convert to lowercase
        $slug = strtolower($title);

        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Remove leading/trailing hyphens
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Find post by slug
     * @param string $slug Post slug
     * @return Post|null Post instance or null if not found
     */
    public static function findBySlug($slug) {
        return static::findFirst(['slug' => $slug]);
    }

    /**
     * Create post with validation
     * @param array $data Post data
     * @return static Created post instance
     * @throws Exception If validation fails
     */
    public static function create(array $data) {
        // Validate required fields
        if (empty($data['title'])) {
            throw new Exception('Title is required');
        }

        if (empty($data['content'])) {
            throw new Exception('Content is required');
        }

        if (empty($data['user_id'])) {
            throw new Exception('User ID is required');
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = static::generateSlug($data['title']);

            // Ensure slug is unique
            $counter = 1;
            $baseSlug = $data['slug'];
            while (static::findBySlug($data['slug'])) {
                $data['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'draft';
        }

        // Set published_at if publishing
        if ($data['status'] === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        // Initialize view count
        if (!isset($data['view_count'])) {
            $data['view_count'] = 0;
        }

        // Call parent create method
        return parent::create($data);
    }

    /**
     * Update post with validation
     * @param array $data Data to update
     * @return bool True if successful
     * @throws Exception If validation fails
     */
    public function update(array $data) {
        // If title is being changed, update slug if needed
        if (!empty($data['title']) && $data['title'] !== $this->getTitle()) {
            if (empty($data['slug'])) {
                $data['slug'] = static::generateSlug($data['title']);

                // Ensure slug is unique
                $counter = 1;
                $baseSlug = $data['slug'];
                while (true) {
                    $existing = static::findBySlug($data['slug']);
                    if (!$existing || $existing->getValue('id') === $this->getValue('id')) {
                        break;
                    }
                    $data['slug'] = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }
        }

        // If slug is being changed, check uniqueness
        if (!empty($data['slug']) && $data['slug'] !== $this->getSlug()) {
            $existing = static::findBySlug($data['slug']);
            if ($existing && $existing->getValue('id') !== $this->getValue('id')) {
                throw new Exception('Slug already exists');
            }
        }

        // Set published_at if changing to published
        if (!empty($data['status']) && $data['status'] === 'published' && !$this->isPublished()) {
            if (!isset($data['published_at'])) {
                $data['published_at'] = date('Y-m-d H:i:s');
            }
        }

        // Call parent update method
        return parent::update($data);
    }

    /**
     * Get recent posts
     * @param int $limit Number of posts to retrieve
     * @return array Array of Post entities
     */
    public static function getRecent($limit = 10) {
        $query = db()->select('*')
            ->from(static::$table)
            ->where('status = ?', ['published'])
            ->orderBy('published_at', 'DESC')
            ->limit($limit);

        $results = $query->get();
        $posts = [];

        foreach ($results as $row) {
            $posts[] = new static($row, true);
        }

        return $posts;
    }

    /**
     * Search posts by keyword
     * @param string $keyword Search keyword
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of Post entities
     */
    public static function search($keyword, $limit = null, $offset = 0) {
        $query = db()->select('*')
            ->from(static::$table)
            ->where('(title LIKE ? OR content LIKE ?) AND status = ?',
                   ["%$keyword%", "%$keyword%", 'published']);

        if ($limit !== null) {
            $query->limit($limit, $offset);
        }

        $results = $query->get();
        $posts = [];

        foreach ($results as $row) {
            $posts[] = new static($row, true);
        }

        return $posts;
    }
}