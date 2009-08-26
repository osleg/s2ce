from chatserver import ChatClientFactory
from irc import RelayBotFactory
from event import Event

class Controller:
    
    def __init__(self, chat_client, irc_client):
        self.chat_client = chat_client
        self.irc_client = irc_client
        
        # Subscribe to events
        self.irc_client.on_command += self.on_irc_command
        self.chat_client.on_join += self.on_chat_join
        self.chat_client.on_leave += self.on_chat_leave
        self.chat_client.on_message += self.on_chat_message
        
        # Options
        self.show_meta = False

    # Event handling
        
    def on_irc_command(self, msg):
    	# !send message
        if msg.startswith("!send "):
            self.chat_client.send_message(msg[5:len(msg)])
        # !set meta 1/0
        elif msg.startswith("!set "):
            args = msg.split(" ")
            if args[1] == "meta":
                if args[2] == "1":
                    self.show_meta = True
                if args[2] == "0":
                    self.show_meta = False
    
    def on_chat_join(self, name):
        if self.show_meta:
            self.irc_client.send_message("Player join: %s" % name)
            
    def on_chat_leave(self, name):
        if self.show_meta:
            self.irc_client.send_message("Player left: %s" % name)
            
    def on_chat_message(self, nick, msg):
        self.irc_client.send_message("<%s> %s" % (nick, msg))