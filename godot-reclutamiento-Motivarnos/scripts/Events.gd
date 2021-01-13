extends HTTPRequest

signal finished
signal error

#const ENDPOINT = "http://jeronimoschreyer.me/rrhh/"
const ENDPOINT = "http://localhost:8080/"

var user_id
var start_time
var current_session_id

# Called when the node enters the scene tree for the first time.
func _ready():
	start_time = OS.get_ticks_msec()

func post(action, parameters = {}):
	var headers = ["Content-Type: application/json"]
	request(ENDPOINT + action, headers, false, HTTPClient.METHOD_POST, JSON.print(parameters))
	
func new_player(_name):
	post("player", {"name": _name})
	user_id = yield(self, "finished")
	print("new_player - %s" % _name)

func new_level(level):
	post("session", {"user_id": user_id, "level":level})
	current_session_id = yield(self, "finished")
	start_time()
	print("new_level - %d (session-id=%s) " % [level, current_session_id])

func observation(text):
	post("observation", {"user_id": user_id, "text": text})
	print("observation - %s" % text)

func action(type, metadata={}, time = -1):
	if time == -1:
		time = (OS.get_ticks_msec() - start_time) / 1000.0
	
	post("event", {"session_id": current_session_id, "action": type, "metadata":metadata, "time": time})
	print("action - %s" % type)

func handler(result, response_code, headers, body):
	#var json
	match response_code:
		200:
			#json = JSON.parse(body.get_string_from_utf8())
			#emit_signal("finished", json)
			emit_signal("finished", body.get_string_from_utf8())
		_:
			emit_signal("error", response_code)

func start_time():
	start_time = OS.get_ticks_msec()
