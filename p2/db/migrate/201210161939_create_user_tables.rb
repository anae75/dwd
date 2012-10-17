class CreateUserTables < ActiveRecord::Migration

  def self.up
    create_table :users, :primary_key => :user_id do |t|
      t.integer :created
      t.integer :modified
      t.string :token, :null => false
      t.string :password, :null => false
      t.string :first_name, :null => false
      t.string :last_name, :null => false
      t.string :email, :null => false
    end

  end

  def self.down
    drop_table :users
  end

end

