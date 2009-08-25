import httplib, urllib, re, sys

def get_auth_token(host, url, username, password):
	"""
	Querys the master server for the authentification token that is
	needed to login to the chat server.
	"""
	# Create parameters and headers
	params = "f=auth&email=%s&password=%s" % (username, password)
	headers = {
		"User-Agent": "PHP Script",
		"Content-Type": "application/x-www-form-urlencoded"
	}
	
	# Send request to master server
	conn = httplib.HTTPConnection(host)
	conn.request("POST", url, params, headers)
	response = conn.getresponse()
	
	if response.status != 200:
		print "Can't get authentification from masterserver"
		sys.exit()
	
	# Receive complete response and close connection
	data = response.read()
	conn.close()
	
	# Use regexp to get the token
	m = re.search('"cookie";\w:\d*:"([0-9a-f]{32})"', data)	
	return m.group(1)