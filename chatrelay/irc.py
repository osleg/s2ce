import logging

from twisted.words.protocols import irc
from twisted.internet.protocol import ReconnectingClientFactory

from event import Event

class RelayBot(irc.IRCClient):
    def _get_nickname(self):
        return self.factory.nickname
    nickname = property(_get_nickname)

    def signedOn(self):
        self.join(self.factory.channel)
        
    def connectionMade(self):
        irc.IRCClient.connectionMade(self)
        self.factory.echoers.append(self)
        
    def connectionLost(self, reason):
        irc.IRCClient.connectionLost(self, reason)
        self.factory.echoers.remove(self)
        
    def privmsg(self, user, channel, msg):
        if msg.startswith("!"):
            self.factory.on_command(msg)

class RelayBotFactory(ReconnectingClientFactory):
    protocol = RelayBot

    def __init__(self, channel, nickname):
        # Save persistent information
        self.channel = channel
        self.nickname = nickname
        self.echoers = []
        
        # Events that can be subscribed to
        self.on_command = Event()

	# Connection management
    def clientConnectionLost(self, connector, reason):
    	logging.error("Lost connection to IRC server: %s", reason)
        ReconnectingClientFactory.clientConnectionLost(self, connector, reason)

    def clientConnectionFailed(self, connector, reason):
        logging.error("Failed connection to IRC server: %s", reason)
        ReconnectingClientFactory.clientConnectionFailed(self, connector, reason)	

    # Send messages to IRC        
    def send_message(self, message):
        for echoer in self.echoers:
            echoer.msg(self.channel, message)