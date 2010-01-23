import httplib, urllib, re, sys, logging

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
    logging.debug("Sending request to auth server")
    conn = httplib.HTTPConnection(host)
    conn.request("POST", url, params, headers)
    response = conn.getresponse()
    
    print response.status
    
    if response.status != 200:
        print "Can't get authentification from masterserver"
        sys.exit()
    
    # Receive complete response and close connection
    data = response.read()  
    conn.close()
    logging.debug("Received answer from auth server")
    
    # Use regexp to get the token
    m = re.search('"cookie";\w:\d*:"([0-9a-f]{32})"', data) 
    token = m.group(1)
    logging.info("Received auth token: %s" % token)
    return token    