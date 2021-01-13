extends Control

var open = false
var timer

# Called when the node enters the scene tree for the first time.
func _ready():
	timer = get_parent().get_node("Timer")

func toggle():
	open = false if open else true
	play_anim()

func _show(value):
	open = value
	play_anim()

func play_anim():
	if open:
		Events.action("HELP")
	
	if timer: timer.paused = open
	
	var anim = "in" if open else "out"
	$AnimationPlayer.play("fade %s" % anim)

func _on_Button_pressed():
	toggle()

func _on_Close_pressed():
	_show(false)
