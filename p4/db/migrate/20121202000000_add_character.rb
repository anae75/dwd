class AddCharacter < ActiveRecord::Migration
  def up
    create_table :characters do |t|
      t.references :user
      t.string :name                    # varchar 255
      t.string :description
      t.boolean :npc, :null => false, :default => false
      t.timestamps
    end

    create_table :images do |t|
      t.references :user
      t.references :character
      t.string :filename
      t.timestamps
    end

    create_table :scenes do |t|
      t.references :user
      t.string :title, :null => false, :default => "untitled"
      t.timestamps
    end

    create_table :shots do |t|
      t.references :scene
      t.integer :seq
      t.string :caption
      t.string :text
      t.timestamps
    end

    create_table :positions do |t|
      t.references :shot
      t.string     :type            # hero, party, npc
      t.integer    :image_id
      t.integer    :posx
      t.integer    :posy
      t.integer    :scale
      t.string     :dialog
      t.boolean    :prompt_dialog, :default => false
      t.boolean    :prompt_drawing, :default => false
      t.timestamps
    end

    create_table :stories do |t|
      t.references :user
      t.integer :current_scene, :default => 0
      t.boolean :completed, :default => false
      t.integer :hero_id
      t.integer :companion_1_id
      t.integer :companion_2_id
      t.integer :companion_3_id
      t.timestamps
    end

    create_table :story_scenes do |t|
      t.references :story
      t.references :scene
      t.integer    :seq
      t.timestamps
    end

    create_table :responses do |t|
      t.references :user
      t.references :character
      t.references :shot
      t.string :text
      t.string :image_filename
      t.timestamps
    end

  end
    
  def down
    drop_table :responses
    drop_table :positions
    drop_table :shots
    drop_table :scenes
    drop_table :images
    drop_table :characters
    drop_table :story_scenes
    drop_table :stories
  end
end
