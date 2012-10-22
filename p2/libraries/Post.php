<?php

class Post {

  # return max most recent posts
  public static function recent($max=10) 
  {
    $sql = <<<END_SQL
      select users.user_id, users.first_name, users.last_name, posts.created, posts.text
      from posts
        inner join users on posts.user_id = users.user_id
      order by posts.created desc
      limit $max
END_SQL;
    $posts = DB::instance(DB_NAME)->select_rows($sql, "object");
    return $posts;
  }

} # end class
