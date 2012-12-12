
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
    v = 1 if v.is_a?(TrueClass)
    v = 0 if v.is_a?(FalseClass)
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

  @susie, @susie_img = create_character :name => "Susie",
                          :description => "Just Another T Rider",
                          :user_id => @user_id,
                          :filename => filename("char_04_susie.png")

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
                     :posx => 200, :posy => 100, :scale => 1,
                     :dialog => "It''s the transit police!"

  # police:  Bag check!
  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => transit_police_01, :posx => 0, :posy => 0, :scale => 1,
                     :dialog => "Bag check!"

  # prompt for input
  shot_id = create_shot :scene_id => scene_id, :text => "Do you have anything in your bag to distract the police?"
  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 200, :posy => 100, :scale => 1,
                     :prompt_dialog => true, :prompt_drawing => true

  # create a response from sob story guy
  response_id = create_response :user_id => user_id,
                                :shot_id => shot_id, :character_id => @sob_story_guy,
                                :image_filename => "images/money_01.png",
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
  shot_id = create_shot :scene_id => scene_id, :text => "Whew!!"

  scene_id
end # create_transit_police_scene

def create_penelope_scene
  scene_id = create_scene :user_id => @user_id, :title => "Snakes on a Train"

  # penelope
  npc_id = create_npc :name => "Penelope", :user_id => @user_id
  penelope_01 = create_image :character_id => npc_id, :user_id => @user_id, :filename => filename("penelope_01.png")
  penelope_02 = create_image :character_id => npc_id, :user_id => @user_id, :filename => filename("penelope_02.png")

  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :dialog => "Hisssssss!",
                     :image_id => penelope_01, :posx => 0, :posy => 0, :scale => 1

  # hero:  It's penelope!
  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 200, :posy => 100, :scale => 1,
                     :dialog => "It''s Penelope!" 

  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 200, :posy => 100, :scale => 1,
                     :dialog => "The lost Red Line snake!" 

  shot_id = create_shot :scene_id => scene_id, :text => "She''s grown pretty big."

  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 200, :posy => 100, :scale => 1,
                     :dialog => "...and hungry." 

  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :dialog => "You look delicious.",
                     :image_id => penelope_02, :posx => 0, :posy => 0, :scale => 1

  shot_id = create_shot :scene_id => scene_id, :text => "Do you have any food to distract her?"

  shot_id = create_shot :scene_id => scene_id, :caption => "Draw something for Penelope to eat."
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 200, :posy => 100, :scale => 1,
                     :prompt_dialog => true, :prompt_drawing => true

  # responses for stock characters
  # sob story guy
  response_id = create_response :user_id => @user_id,
                                :shot_id => shot_id, :character_id => @sob_story_guy,
                                :text => "My parole office won''t let me talk to snakes."

  # headphones guy
  response_id = create_response :user_id => @user_id,
                                :shot_id => shot_id, :character_id => @headphones_guy,
                                :text => "You can have the foam from my headphones.  Yum!" 

  # backpack guy
  response_id = create_response :user_id => @user_id,
                                :shot_id => shot_id, :character_id => @backpack_guy,
                                :text => "I always carry a spare backpack."

  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :dialog => "Chomp chomp chomp",
                     :image_id => penelope_01, :posx => 0, :posy => 0, :scale => 1

  shot_id = create_shot :scene_id => scene_id, :text => "Let''s get away while we can."

  scene_id
end

def create_track_fire_scene
  scene_id = create_scene :user_id => @user_id, :title => "Track Fire"

  # create the main character
  npc_id = create_npc :name => "Track Fire", :user_id => @user_id
  track_fire_01 = create_image :character_id => npc_id, :user_id => @user_id, :filename => filename("track_fire_01.png")
  track_fire_02 = create_image :character_id => npc_id, :user_id => @user_id, :filename => filename("track_fire_02.png")

  shot_id = create_shot :scene_id => scene_id, :text => "What''s that smell?"

  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => track_fire_02, :posx => 0, :posy => 0, :scale => 1

  # hero
  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => track_fire_01, :posx => 0, :posy => 0, :scale => 1
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 300, :posy => 100, :scale => 1,
                     :dialog => "It''s a track fire!" 

  shot_id = create_shot :scene_id => scene_id, :text => "How can we get across this fire?"

  shot_id = create_shot :scene_id => scene_id, :caption => "Draw something to help you cross the fire."
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => track_fire_01, :posx => 0, :posy => 0, :scale => 1
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 300, :posy => 100, :scale => 1,
                     :prompt_dialog => true, :prompt_drawing => true

  # responses
  # sob story guy
  response_id = create_response :user_id => @user_id,
                                :shot_id => shot_id, :character_id => @sob_story_guy,
                                :text => "I can cry a lot!  I can cry a river!"

  # headphones guy
  response_id = create_response :user_id => @user_id,
                                :shot_id => shot_id, :character_id => @headphones_guy,
                                :text => "I can play soothing music..." 

  # backpack guy
  response_id = create_response :user_id => @user_id,
                                :shot_id => shot_id, :character_id => @backpack_guy,
                                :text => "I''ve got a water bottle in here somewhere..."

  shot_id = create_shot :scene_id => scene_id, :text => "That''s better!"


  scene_id
end

def create_locked_door_scene
  scene_id = create_scene :user_id => @user_id, :title => "Locked Door"

  # create the main character
  npc_id = create_npc :name => "A Locked Door", :user_id => @user_id
  locked_door_01 = create_image :character_id => npc_id, :user_id => @user_id, :filename => filename("locked_door_01.png")
  locked_door_02 = create_image :character_id => npc_id, :user_id => @user_id, :filename => filename("locked_door_02.png")

  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => locked_door_01, :posx => 0, :posy => 0, :scale => 1

  # hero
  shot_id = create_shot :scene_id => scene_id
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => locked_door_01, :posx => 0, :posy => 0, :scale => 1
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 300, :posy => 100, :scale => 1,
                     :dialog => "Hmm, it''s a locked door..." 

  shot_id = create_shot :scene_id => scene_id, :text => "How can we open the door?"

  shot_id = create_shot :scene_id => scene_id, :caption => "Draw something to open the door."
  line = create_position :shot_id => shot_id, :type => :npc, 
                     :image_id => locked_door_01, :posx => 0, :posy => 0, :scale => 1
  line = create_position :shot_id => shot_id, :type => :hero, 
                     :posx => 300, :posy => 100, :scale => 1,
                     :prompt_dialog => true, :prompt_drawing => true
  # responses
  # sob story guy
  response_id = create_response :user_id => @user_id,
                                :shot_id => shot_id, :character_id => @sob_story_guy,
                                :text => "I can look really sad."

  # headphones guy
  response_id = create_response :user_id => @user_id,
                                :shot_id => shot_id, :character_id => @headphones_guy,
                                :text => "Guitar pick?" 

  # backpack guy
  response_id = create_response :user_id => @user_id,
                                :shot_id => shot_id, :character_id => @backpack_guy,
                                :text => "That door is too small for my backpack."


  shot_id = create_shot :scene_id => scene_id, :text => "We''re in!  I wonder what''s on the other side?"
  scene_id
end

def create_story
  sql = sql_for_insert("stories", :user_id => @user_id, 
                                  :hero_id => @susie,
                                  :completed => 0,
                                  :current_scene => 0,
                                  :companion_1_id => @sob_story_guy, 
                                  :companion_2_id => @backpack_guy, 
                                  :companion_3_id => @headphones_guy, 
                                  )
  nrows = @connection.insert sql
  story_id = last_row("stories") 

  sql = sql_for_insert("story_scenes", :story_id => story_id, :scene_id => @transit_police_scene, :seq => 1)
  nrows = @connection.insert sql
  sql = sql_for_insert("story_scenes", :story_id => story_id, :scene_id => @penelope_scene, :seq => 2)
  nrows = @connection.insert sql
  sql = sql_for_insert("story_scenes", :story_id => story_id, :scene_id => @track_fire_scene, :seq => 3)
  nrows = @connection.insert sql
  sql = sql_for_insert("story_scenes", :story_id => story_id, :scene_id => @locked_door_scene, :seq => 0)
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
@penelope_scene = create_penelope_scene
@track_fire_scene = create_track_fire_scene
@locked_door_scene = create_locked_door_scene
story_id = create_story

