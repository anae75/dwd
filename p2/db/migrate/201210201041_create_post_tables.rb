class CreatePostTables < ActiveRecord::Migration

  def self.up
    create_table :posts do |t|
      t.integer :created
      t.integer :modified
      t.string :text, :null => false
      t.references :user
    end

    create_table :streams do |t|
      t.string :name, :null => false
      t.text :description
      t.references :user
    end

    create_table :users_followers do |t|
      t.integer :user_id
      t.integer :follower_id
      t.integer :stream_id
    end

  end

  def self.down
    drop_table :users_followers
    drop_table :streams
    drop_table :posts
  end

end

