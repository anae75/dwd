
# usage:  bundle exec ruby dbseed.rb 

require "rubygems"
require "bundler/setup"
require 'active_record'

require 'digest/sha1'


PASSWORD_SALT = '3457DFc43#$!$#%(8fhgljgd'
TOKEN_SALT = '#%&^)JOIL;HFPIEFD8F!$#NP8FXVG'

ActiveRecord::Base.establish_connection(YAML.load(File.read(File.join('.','database.yml')))[ENV['ENV'] ? ENV['ENV'] : 'development'])
@connection = ActiveRecord::Base.connection

@uids = [];

def follow_previous_users(follower_id)
  @uids.each do |user_id|
    next if user_id == follower_id
    sql = "insert into users_followers (user_id, follower_id) values (#{user_id}, #{follower_id})" 
    @connection.execute(sql)
  end
end

def seed_posts_for_user(user_id, name, num)
  (0..num).each do |i|
    t = Time.now.to_i - 60*60*24*i
    text = random_text
    sql = "insert into posts (user_id, text, created, modified) values ('#{user_id}', '#{text}', #{t}, #{t})" 
    #puts sql
    @connection.execute(sql)
  end
end

def seed_user(i)
  first_name = "user#{i}_first"
  last_name  = "user#{i}_last"
  email = "user#{i}@foo.com"
  password = Digest::SHA1.hexdigest PASSWORD_SALT + 'password'
  token = Digest::SHA1.hexdigest TOKEN_SALT + email + "3c790salkewecw"

  sql =<<-END_SQL 
  insert into users (first_name, last_name, email, password, token, created, modified) 
    values ('#{first_name}', '#{last_name}', '#{email}', '#{password}', '#{token}', #{Time.now.to_i}, #{Time.now.to_i})
  END_SQL
  #puts sql
  user_id = nil
  begin
    insertresult = @connection.execute sql
    user = @connection.execute "select user_id from users where email='#{email}'"
    user_id = user.fetch_row.first
    puts "#{email} #{user_id}"
    @uids << user_id
  ensure
    insertresult.free if insertresult
    user.free if user
  end

  seed_posts_for_user(user_id, first_name, 10) if user_id
  follow_previous_users(user_id);
end

SAYINGS = [
  "the cat ate my homework",
  "I am sleepy!",
  "I am hungry!",
  "I am scared!",
  "I can haz cheezburger?",
  "Mommy!",
  "I hate php",
  "Are we there yet?",
  "Are we having fun yet?",
  "blah blah blah",
  "blah blah blah blah blah",
  "blah blah blah blah blah blah blah",
  "The homework ate my cat",
  "spaghetti!!!",
  "The dead are walking...",

  "Mother said, straight ahead,",
  "Not to delay, or be mislead.",
  "I should have heeded her advice.",
  "But he seemed so nice.",
  "And he showed me things, many beautiful things,",
  "That I hadnt thought to explore.",
  "They were off my path, so I never had dared.",
  "I had been so careful, I never had cared.",
  "And he made me feel excited..",
  "Well, excited and scared.",
  "When he said, Come in, with that sickening grin,",
  "How could I know what was in store?",
  "Once his teeth were bared, though, I really got scared.",
  "Well, excited and scared..",
  "But he drew me close, and he swallowed me down,",
  "Down a dark, slimy path, where lie secrets that I never want to know,",
  "And when everything familiar seemed to disappear forever,",
  "At the end of the path, was Granny once again,",
  "So we wait in the dark, until someone sets us free,",
  "And were brought into the light,",
  "And were back at the start..",
  "And I know things now, many valuable things,",
  "That I hadnt known before.",
  "Do not put your faith in a cape and a hood.",
  "They will not protect you the way that they should.",
  "And take extra care with strangers, even flowers have their dangers,",
  "And though scary is exciting,",
  "Nice is different than good.",
  "Now I know, dont be scared.  Granny is right, just be prepared.",
  "Isnt it nice to know a lot?",
  "..And a little bit.. not."
]

def random_text
  SAYINGS[rand(SAYINGS.length)]  
end

#seed_user(6)
#seed_posts_for_user(10, "ana", 1)

(0..10).each { |i| seed_user(i) }
