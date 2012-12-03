
# usage:  bundle exec ruby dbseed.rb 

require "rubygems"
require "bundler/setup"
require 'active_record'

require 'digest/sha1'

require 'optparse'

def filename(f)
  "images/#{f}"
end

PASSWORD_SALT = '3457DFc43#$!$#%(8fhgljgd'
TOKEN_SALT = '#%&^)JOIL;HFPIEFD8F!$#NP8FXVG'

ActiveRecord::Base.establish_connection(YAML.load(File.read(File.join('.','database.yml')))[ENV['ENV'] ? ENV['ENV'] : 'development'])
@connection = ActiveRecord::Base.connection

def reset_db
  tables = %w{ characters images 
                scenes shots positions responses
                stories story_scenes }
  tables.reverse.each do |table|
    puts "deleting #{table}"
    @connection.execute "delete from #{table} where 1" 
  end
end

def timestamp
  Time.now.strftime "%Y/%m/%d %H:%M:%S"
end

def sql_for_insert(table, opts)
  cols = ""
  vals = ""
  opts.each_pair do |k,v|
    cols += ", #{k}"
    vals += ", '#{v}'"
  end
  "insert into #{table} (created_at, updated_at #{cols}) values ('#{timestamp}', '#{timestamp}' #{vals})"
end

def last_row(table)
  @connection.select_value "select id from #{table} order by id desc limit 1"
end


# create superuser Ana
def create_superuser
  first_name, last_name = "Ana", ""
  email = "ana@foo.com"
  password = Digest::SHA1.hexdigest PASSWORD_SALT + 'password'
  token = Digest::SHA1.hexdigest TOKEN_SALT + email + "3c790salkewecw"

  sql =<<-END_SQL 
  insert into users (first_name, last_name, email, password, token, superuser, created, modified) 
    values ('#{first_name}', '#{last_name}', '#{email}', '#{password}', '#{token}', 1, #{Time.now.to_i}, #{Time.now.to_i})
  END_SQL
  #puts sql

  user_id = nil
  begin
    # see if user already exists
    user = @connection.execute("select user_id from users where email='#{email}'")
    if row = user.fetch_row
      user_id = row.first
      puts "user #{email} (#{user_id}) exists" 
      return user_id
    end
    # else create new user
    insertresult = @connection.execute sql
    user = @connection.execute "select user_id from users where email='#{email}'"
    user_id = user.fetch_row.first
    puts "created #{email} #{user_id}"
  ensure
    insertresult.free if insertresult
    user.free if user
  end
  user_id
end

def create_character(opts)
  sql = sql_for_insert("characters", opts.select { |x| [:name, :description, :user_id].include?(x) })
  nrows = @connection.insert sql
  character_id = last_row "characters"

  sql = sql_for_insert("images", :user_id => opts[:user_id], :character_id => character_id, :filename => opts[:filename])
  nrows = @connection.insert sql
  image_id = last_row("images") 

  [character_id, image_id]
end

def create_npc(opts)
  sql = sql_for_insert("characters", opts.merge( {:npc => 1} ) )
  nrows = @connection.insert sql
  character_id = last_row "characters"
end

def create_image(opts)
  sql = sql_for_insert("images", opts)
  nrows = @connection.insert sql
  image_id = last_row("images") 
end

def create_scene(opts)
  sql = sql_for_insert("scenes", opts)
  nrows = @connection.insert sql
  id = last_row("scenes") 
end

@shot_seq = Hash.new(0)
def create_shot(opts)
  sql = sql_for_insert("shots", opts.merge({:seq => @shot_seq[opts[:shot_id]] }))
  @shot_seq[opts[:shot_id]] += 1
  nrows = @connection.insert sql
  id = last_row("shots") 
end

def create_position(opts)
  # XXX handle single quotes
  sql = sql_for_insert("positions", opts)
  nrows = @connection.insert sql
  id = last_row("positions") 
end

def create_response(opts)
  # XXX handle single quotes
  sql = sql_for_insert("responses", opts)
  nrows = @connection.insert sql
  id = last_row("responses") 
end

def create_default_characters
  # create some characters
  @sob_story_guy, @sob_story_guy_img = create_character :name => "Sob Story Guy",
                          :description => "A morose looking fellow",
                          :user_id => @user_id,
                          :filename => filename("char_01_sob_story_guy.png")
  @headphones_guy, @headphones_guy_img = create_character :name => "Guy With Headphones",
                          :description => "What?  I can''t hear you!",
                          :user_id => @user_id,
                          :filename => filename("char_02_headphones.png")

  @backpack_guy, @backpack_guy_img = create_character :name => "Guy With Backpack",
                          :description => "",
                          :user_id => @user_id,
                          :filename => filename("char_03_big_backpack.png")
end

def create_transit_police_scene
  user_id = @user_id

  # create default scenes
  scene_id = create_scene :user_id => user_id, :title => "Transit Police"

  # transit police 
  npc_id = create_npc :name => "Transit Police", :user_id => user_id
  transit_police_01 = create_image :character_id => npc_id, :user_id => user_id, :filename => filename("transit_police_01.png")
  transit_police_02 = create_image :character_id => npc_id, :user_id => user_id, :filename => filename("transit_police_02.png")

  # TODO walking up to transit police

  # transit police standing
  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => transit_police_01, :posx => 0, :posy => 0, :scale => 1

  # hero:  It's the transit police!
  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 0, :posy => 0, :scale => 1,
                     :dialog => "It''s the transit police!"

  # police:  Bag check!
  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => transit_police_01, :posx => 0, :posy => 0, :scale => 1,
                     :dialog => "Bag check!"

  # prompt for input
  shot_id = create_shot :scene_id => scene_id, :caption => "Do you have anything in your bag to distract the police?"
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 0, :posy => 0, :scale => 1,
                     :prompt_dialog => true, :prompt_drawing => true

  # create a response from sob story guy
  response_id = create_response :user_id => user_id,
                                :shot_id => shot_id, :character_id => @sob_story_guy,
                                :text => "I have some money from people who felt sorry for me." 

  # create a response from headphones guy
  response_id = create_response :user_id => user_id,
                                :shot_id => shot_id, :character_id => @headphones_guy,
                                :text => "What?  This song is really good.  What?" 

  # create a response from backpack guy
  response_id = create_response :user_id => user_id,
                                :shot_id => shot_id, :character_id => @backpack_guy,
                                :text => "My bag is so big it needs its own T pass."

  # police:  Carry on.
  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => transit_police_01, :posx => 0, :posy => 0, :scale => 1,
                     :dialog => "Carry on."

  scene_id
end # create_transit_police_scene

def create_story
  sql = sql_for_insert("stories", :user_id => @user_id, 
                                  :hero_id => @sob_story_guy,
                                  :current_scene => 0,
                                  :companion_1_id => @sob_story_guy, 
                                  :companion_2_id => @backpack_guy, 
                                  :companion_3_id => @headphones_guy, 
                                  )
  nrows = @connection.insert sql
  story_id = last_row("stories") 

  sql = sql_for_insert("story_scenes", :story_id => story_id, :scene_id => @transit_police_scene)
  nrows = @connection.insert sql

  story_id
end

options = {}
OptionParser.new do |opts|
  opts.banner = "Usage: dbseed.rb [options]"
end.parse!

reset_db
@user_id = create_superuser
create_default_characters
@transit_police_scene = create_transit_police_scene
story_id = create_story

