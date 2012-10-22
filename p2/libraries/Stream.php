<?php

class Stream {

  const default_stream_id = 0;

  public $errors;
  public $id;
  public $user_id;
  public $name;
  public $description;
  public $following;

  public function __construct($stream_id, $user_id, $name, $description="")
  {
    $this->errors = [];
    $this->id = $stream_id;
    $this->user_id = $user_id;
    $this->name = $name;
    $this->description = $description;
    $this->following = [];
    $this->load_stream();
  } 

  private function load_stream()
  {
    $sql = <<<END_SQL
      select users.user_id, users.first_name, users.last_name 
      from users_followers uf
        inner join users on uf.user_id = users.user_id 
      where uf.follower_id=$this->user_id and uf.stream_id=$this->id
END_SQL;
    $this->following = DB::instance(DB_NAME)->select_rows($sql, "object");
  }

  # XXX support data range and limit
  public function posts()
  {
    $uids = []; 
    foreach($this->following as $f) {
      $uids[] = $f->user_id;
    }
    if(empty($uids)) {
      return [];
    }
    $uids_sql = join(',', $uids);
    $sql = <<<END_SQL
      select posts.created, posts.text, users.user_id, users.first_name, users.last_name
      from posts
        inner join users on users.user_id=posts.user_id
      where posts.user_id in ($uids_sql)
      order by created desc
END_SQL;
    $posts = DB::instance(DB_NAME)->select_rows($sql, "object");
    return $posts;
  }

  static public function move_stream($follower_id, $user_id, $stream_id) 
  {
    $where_sql = sprintf(" where user_id=%d and follower_id=%d", $user_id, $follower_id);
    $data = Array("stream_id" => $stream_id);
    DB::instance(DB_NAME)->update("users_followers", $data, $where_sql);
    return true;
  }

} # end class
