from twisted.internet import reactor

from masterserver import get_auth_token
from chatserver import ChatClientFactory
from irc import RelayBotFactory

# Configuration
CHATHOST="chatserver.savage2.s2games.com"
CHATPORT=11030
MASTERHOST="masterserver.savage2.s2games.com"
MASTERPORT=80
MASTERURL="/irc_updater/irc_requester.php"
IRCHOST="irc.newerth.com"
IRCPORT=6667

USERNAME="ChatBot"
PASSWORD="roboter"
ACCOUNTID=350570
IRCNAME="Sav2Lobby"
IRCCHANNEL="#sav2relay"

def main():
	# To login to the chat server, we need an authentification token which
	# we can receive by logging into the master server.
	token = get_auth_token(MASTERHOST, MASTERURL, USERNAME, PASSWORD)
	
	# Create factory for IRC connection
	i = RelayBotFactory(IRCCHANNEL, IRCNAME)
	
	# Create factory protocol and application for chat server
	f = ChatClientFactory(token, ACCOUNTID)
	
	# Connect factories between each other
	f.set_relay_bot(i)
	i.set_chat_client(f)
	
	# Connect to network
	reactor.connectTCP(CHATHOST, CHATPORT, f)
	reactor.connectTCP(IRCHOST, IRCPORT, i)
	
	# Run bot
	reactor.run()

# Only run when module was not imported
if __name__ == '__main__':
	main()