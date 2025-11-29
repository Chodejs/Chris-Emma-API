<?php 
class Post {
    private $conn;
    private $table = 'posts';

    public $id;
    public $category;
    public $title;
    public $body;
    public $author;
    public $image;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' SET title = :title, body = :body, author = :author, category = :category, image = :image';
        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->body = $this->body; 
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->image = htmlspecialchars(strip_tags($this->image));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':body', $this->body);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':image', $this->image);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>