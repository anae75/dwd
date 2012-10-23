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
    $this->errors = Array();
    $this->id = $stream_id;
    $this->user_id = $user_id;
    $this->name = $name;
    $this->description = $description;
    $this->following = Array();
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
    $uids = Array(); 
    foreach($this->following as $f) {
      $uids[] = $f->user_id;
    }
    if(empty($uids)) {
      return Array();
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

  # move a followed user from one stream to another
  static public function move_stream($follower_id, $user_id, $stream_id) 
  {
    $where_sql = sprintf(" where user_id=%d and follower_id=%d", $user_id, $follower_id);
    $data = Array("stream_id" => $stream_id);
    DB::instance(DB_NAME)->update("users_followers", $data, $where_sql);
    return true;
  }

  static public function delete_stream($user_id, $stream_id) 
  {
    if($stream_id == Stream::default_stream_id) {
      return false;
    }
    if(MyUser::user_exists($user_id)) {
      # move all users in this stream to the default stream
      $where_sql = sprintf(" where follower_id=%d and stream_id=%d", $user_id, $stream_id);
      $data = Array("stream_id" => Stream::default_stream_id);
      DB::instance(DB_NAME)->update("users_followers", $data, $where_sql);
      # delete the stream
      $sql = sprintf(" where user_id=%d and id=%d", $user_id, $stream_id);
      $result = DB::instance(DB_NAME)->delete("streams", $sql);
      return true;
    } 

  }

} # end class
