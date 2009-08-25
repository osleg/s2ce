from twisted.internet import reactor

from masterserver import get_auth_token
from chatserver import ChatClientFactory

# Configuration
CHATHOST="chatserver.savage2.s2games.com"
CHATPORT=11030
MASTERHOST="masterserver.savage2.s2games.com"
MASTERPORT=80
MASTERURL="/irc_updater/irc_requester.php"

USERNAME="ChatBot"
PASSWORD="roboter"
ACCOUNTID=350570

def main():
	# To login to the chat server, we need an authentification token which
	# we can receive by logging into the master server.
	token = get_auth_token(MASTERHOST, MASTERURL, USERNAME, PASSWORD)
	
	# Create factory protocol and application
	f = ChatClientFactory(token, ACCOUNTID)
	
	# Connect factory to this host and port
	reactor.connectTCP(CHATHOST, CHATPORT, f)
	
	# Run bot
	reactor.run()

# Only run when module was not imported
if __name__ == '__main__':
	main()