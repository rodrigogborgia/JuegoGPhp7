extends Node2D

onready var Grid = get_parent()

var tile_history = []
var going_back = false
var out_of_time = false

var right_moves = 0
var wrong_moves = 0
var index_move = 1

func _ready():
	set_process_input(true)

func _input(event):
	if event is InputEventKey and !Grid.won and !out_of_time:
		var input_direction = get_input_direction(event)
		if not input_direction:
			return
		update_look_direction(input_direction)
		
		var is_going_back = going_back
		var target_position = Grid.request_move(input_direction)
		if target_position:
			if Grid.won:
				Events.action("WIN", {"going_back": false, "remaining": 0})
				goto_next_level()
			else:
				index_move += -1 if is_going_back else 1
				right_moves += 1
				Events.action("MOVE", {"going_back": is_going_back, "remaining": Grid.path_size - index_move})
				move_to(target_position)
		else:
			wrong_moves += 1
			Events.action("WRONG", {"going_back": is_going_back, "remaining": Grid.path_size - index_move})
			bump()

func get_input_direction(event):
	if event.is_pressed() and not event.is_echo():
		var direction = Vector2()
		
		if check_input("ui_right", event):
			direction.x = 1
		
		if check_input("ui_left", event):
			direction.x = -1
		
		if check_input("ui_up", event):
			direction.y = -1
		
		if check_input("ui_down", event):
			direction.y = 1
		
		return direction

func check_input(action, event):
	for action_event in InputMap.get_action_list(action):
		if action_event.shortcut_match(event):
			return true
	return false

func update_look_direction(direction):
	$Pivot/Sprite.rotation = direction.angle()

func move_to(target_position):
	set_process_input(false)
	
	$AnimationPlayer.play("walk")
	position = target_position
	
	yield($AnimationPlayer, "animation_finished")
	set_process_input(true)


func bump():
	set_process_input(false)
	
	$AnimationPlayer.play("bump")
	
	yield($AnimationPlayer, "animation_finished")
	set_process_input(true)

func goto_next_level():
	# Go to next level
	var next_level = "res://levels/Level_%d.tscn" % (Grid.level + 1)
	if ResourceLoader.exists(next_level):
		yield(get_tree().create_timer(3), "timeout")
		get_tree().change_scene(next_level)
	else:
		yield(get_tree().create_timer(3), "timeout")
		get_tree().change_scene("res://game/Observaciones.tscn")

func _on_Timer_out_of_time():
	out_of_time = true
	Grid.set_visible_path(true)
	goto_next_level()
