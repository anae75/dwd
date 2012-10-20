
# usage:  bundle exec ruby dbseed.rb 

require "rubygems"
require "bundler/setup"
require 'active_record'

require 'digest/sha1'


PASSWORD_SALT = '3457DFc43#$!$#%(8fhgljgd'
TOKEN_SALT = '#%&^)JOIL;HFPIEFD8F!$#NP8FXVG'

ActiveRecord::Base.establish_connection(YAML.load(File.read(File.join('.','database.yml')))[ENV['ENV'] ? ENV['ENV'] : 'development'])
@connection = ActiveRecord::Base.connection

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
  puts sql
  user_id = nil
  begin
    insertresult = @connection.execute sql
    user = @connection.execute "select user_id from users where email='#{email}'"
    user_id = user.fetch_row.first
    puts user_id
  ensure
    insertresult.free if insertresult
    user.free if user
  end

  seed_posts_for_user(user_id, first_name, 10) if user_id
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
  "The dead are walking..."
]

def random_text
  SAYINGS[rand(SAYINGS.length)]  
end

seed_user(6)
#seed_posts_for_user(10, "ana", 1)