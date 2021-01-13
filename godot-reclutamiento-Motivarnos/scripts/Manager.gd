extends TileMap

signal won
var won = false

# REMEMBER TO SET SIZE - 1 (Rect2 is 1-based but TileMap tiles position is 0-based)
export (Rect2) var size
export (int) var path_size
export (int) var level

func _ready():
	randomize()
	make_path(path_size)
	
	# Hide path
	set_visible_path(false)
	
	# Start time
	Events.new_level(level)

func set_visible_path(_visible):
	for id in tile_set.get_tiles_ids():
		if id == 2:
			tile_set.tile_set_modulate(id, Color.black if _visible else Color.transparent)

func make_path(path_size):
	var path
	
	# Determine initial position and player direction
	var initial_position
	var intial_direction
	match randi() % 4:
		0: # North
			initial_position = Vector2(size.position.x + randi() % int(size.size.x), size.position.y)
			intial_direction = Vector2(0, 1)
		1: # South
			initial_position = Vector2(size.position.x + randi() % int(size.size.x), size.end.y)
			intial_direction = Vector2(0,-1)
		2: # East
			initial_position = Vector2(size.end.x, size.position.y + randi() % int(size.size.y))
			intial_direction = Vector2(-1, 0)
		3: # West
			initial_position = Vector2(size.position.x, size.position.y + randi() % int(size.size.y))
			intial_direction = Vector2(1, 0)
	
	# Set Player at initial position
	$Player.position = map_to_world(initial_position) + cell_size / 2
	$Player.update_look_direction(intial_direction)
	
	# Iterate
	var pushed_back = 4
	while pushed_back == 4:
		path = PoolVector2Array()
		path.push_back(initial_position)
		pushed_back = 0
		
		while path.size() < path_size:
			var posibilities = PoolVector2Array()
			var movement = PoolVector2Array([Vector2(1, 0), Vector2(0, 1), Vector2(-1, 0), Vector2(0, -1)])
			var current_position = path[path.size() - 1]
			
			for i in range(movement.size()):
				var posible_position = current_position + movement[i]
				if is_inside_grid_limit(posible_position):
					if !is_returning_or_is_touching_path(posible_position, path):
						posibilities.append(movement[i])
			
			# if no posibilities, go back
			if posibilities.size() > 0:
				path.push_back(current_position + posibilities[randi() % posibilities.size()])
			else:
				pushed_back = pushed_back + 1
				if pushed_back > 3:
					break
				else:
					path.remove(path.size() - 1)
	
	# set path tiles
	for pos in path:
		set_cellv(pos, 2)
	
	# set flag at the end of the path
	set_cellv(path[path.size() - 1], 0)

func is_returning_or_is_touching_path(posibilty, path):
	if (path as Array).has(posibilty):
		return true
	
	var adjacent_tiles = PoolVector2Array([Vector2(1, 0), Vector2(0, 1), Vector2(-1, 0), Vector2(0, -1)])
	for adjacent_tile in adjacent_tiles:
		var posible_position = posibilty + adjacent_tile
		if posible_position != path[path.size() - 1]:
			if (path as Array).has(posible_position):
				return true
	
	return false

func is_inside_grid_limit(position):
	if position.x < size.position.x or position.x > size.end.x:
		return false
	
	if position.y < size.position.y or position.y > size.end.y:
		return false
	
	return true

func request_move(direction):
	var cell_start = world_to_map($Player.position)
	var cell_target = cell_start + direction
	
	var cell_target_type = get_cellv(cell_target)
	match cell_target_type:
		2:
			if not $Player.going_back:
				# check that we're not actually going back
				if $Player.tile_history.size() > 1 && cell_target == $Player.tile_history[-1]:
					return
				$Player.tile_history.push_back(cell_start)
			else:
				# check that we're actually going back
				if cell_target != $Player.tile_history[-1]:
					return
				
				$Player.tile_history.pop_back()
				if $Player.tile_history.size() == 0:
					$Player.going_back = false
				
			return map_to_world(cell_target) + cell_size / 2
		0:
			if not $Player.going_back:
				won = true
				emit_signal("won", $Player)
				set_visible_path(true)
				return $Player.position
		_:
			if $Player.tile_history.size() > 0:
				$Player.going_back = true
