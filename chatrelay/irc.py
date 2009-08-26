from twisted.words.protocols import irc
from twisted.internet import protocol

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
    	if msg == "!show_joinleave":
    		self.factory.chat_client.show_joinleave()
    	elif msg == "!hide_joinleave":
    		self.factory.chat_client.hide_joinleave()
    	elif msg.startswith("!send "):
    		self.factory.to_lobby(msg[6:len(msg)])

class RelayBotFactory(protocol.ClientFactory):
    protocol = RelayBot

    def __init__(self, channel, nickname):
        self.channel = channel
        self.nickname = nickname
        self.echoers = []        

    def set_chat_client(self, client):
    	self.chat_client = client
    	
    def clientConnectionLost(self, connector, reason):        
        connector.connect()
        
    def to_irc(self, message):
        for echoer in self.echoers:
            echoer.msg(self.channel, message)                
    
    def to_lobby(self, message):
    	self.chat_client.to_lobby(message)