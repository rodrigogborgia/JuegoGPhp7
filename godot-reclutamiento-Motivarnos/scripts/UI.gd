extends Control

func _on_won(player):
	$Ganaste/Ganaste.text += "\nCORRECTOS: %d\nEQUIVOCADOS: %d" % [player.right_moves, player.wrong_moves]
	$Ganaste.visible = true
	
	
	if has_node("Timer"):
		$Timer.paused = true
