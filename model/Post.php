<?php
namespace Api\Model;

class Post {
    public function __construct() {}

    static public function getAll()
    {
        $query = [
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ];

        $query = new \WP_Query($query);
        return $query->posts;
    }
}
