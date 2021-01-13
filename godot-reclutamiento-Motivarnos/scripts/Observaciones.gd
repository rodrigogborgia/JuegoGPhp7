extends Control


# Declare member variables here. Examples:
# var a = 2
# var b = "text"


# Called when the node enters the scene tree for the first time.
func _ready():
	pass # Replace with function body.


# Called every frame. 'delta' is the elapsed time since the previous frame.
#func _process(delta):
#	pass

func _on_Observaciones_text_changed():
	var text = $Container/Observaciones.text.to_upper()
	$Container/Observaciones.text = ""
	$Container/Observaciones.insert_text_at_cursor(text)
	$Container/Observaciones/AudioStreamPlayer.play()


func _on_Enviar_button_down():
	$Container/Enviar/AudioStreamPlayer.play()

func _on_Enviar_pressed():
	if $Container/Observaciones.text != "":
		$Container/Enviar.disabled = true
		Events.observation($Container/Observaciones.text)
		$Container.visible = false
		$Gracias.visible = true
	
	#get_tree().change_scene("res://game/Gracias.tscn")
