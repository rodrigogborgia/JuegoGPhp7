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


func _on_Comenzar_pressed():
	if $Formulario/Nombre.text != "":
		Events.new_player($Formulario/Nombre.text)
		#yield(Events, "finished") # Librarme cuando se habilite el servidor
		$Formulario.visible = false
		$Instrucciones.visible = true
		
func _on_Comenzar2_pressed():
	get_tree().change_scene("res://levels/Level_1.tscn")

func _on_Comenzar_button_down():
	$Formulario/Siguiente/AudioStreamPlayer.play()

func _on_Nombre_text_changed(new_text):
	var caret_position = $Formulario/Nombre.caret_position
	$Formulario/Nombre.text = new_text.to_upper()
	$Formulario/Nombre.caret_position = caret_position
	$Formulario/Nombre/AudioStreamPlayer.play()
