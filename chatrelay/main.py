from masterserver import get_auth_token

# Configuration
CHATHOST="chatserver.savage2.s2games.com"
CHATPORT=11030
MASTERHOST="masterserver.savage2.s2games.com"
MASTERPORT=80
MASTERURL="/irc_updater/irc_requester.php"

USERNAME="ChatBot"
PASSWORD="roboter"

# To login to the chat server, we need an authentification token which
# we can receive by logging into the master server.
token = get_auth_token(MASTERHOST, MASTERURL, USERNAME, PASSWORD)
print token