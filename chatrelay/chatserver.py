from sys import stdout
from struct import pack, unpack

from twisted.internet.protocol import Protocol, ReconnectingClientFactory

# Packet types
PK_LOGIN=0
PK_WELCOME=1
PK_MESSAGE=4
PK_LIST=5
PK_JOIN=6
PK_LEAVE=7
PK_WHISPER=9

# Protocol
class ChatServer(Protocol):
    # Sending packets
    def send_packet(self, number, params):
        data = pack('b', number)
        for val in params:
            if isinstance(val, int):
                data += self.pack_int(val)
            else:
                data += self.pack_string(val)
        self.transport.write(data)
                
    def pack_byte(self, value):
        return pack('b', value)
    
    def pack_string(self, value):	
        return pack("%ss" % (len(value) + 1), value)
    
    def pack_int(self, value):
        return pack('i', value)
        
    # Receiving data
    def dataReceived(self, data):
        number = unpack('b', data[0])[0]
        
        if number == PK_WELCOME:
            self.welcome()
        elif number == PK_JOIN:
            name = data[1:(len(data)-5)]
            id = unpack('i', data[len(data)-4:len(data)])[0]
            self.join(name, id)
        elif number == PK_LEAVE:
            self.leave(unpack('i', data[1:5])[0])
        elif number == PK_MESSAGE:
            self.message(unpack('i', data[1:5])[0], data[5:len(data)-1])
        else:
            print "Received unknown packet"

    # Callbacks for indiviual packets
    def welcome(self):
        print "Received welcome packet";
    
    def message(self, id, text):
        print "Received message: %s: %s" % (id, text)
        
    def join(self, name, id):
        print "User joined: %s (%s)" % (name, id)
        
    def leave(self, id):
        print "User left: %s" % id

# Relaying implementation
class ChatServerClient(ChatServer):

    def __init__(self):
        self.users = {}
    
    def connectionMade(self):
        self.send_packet(PK_LOGIN, [self.factory.account_id, self.factory.token])
        self.factory.echoers.append(self)
        
    def connectionLost(self, reason):
        self.factory.echoers.remove(self)       
       
    def join(self, name, id):
        self.users[id] = name
        if self.factory.join_leave:
            self.factory.to_irc("Player joined: %s" % name)
        
    def leave(self, id):
        if self.factory.join_leave:
            self.factory.to_irc("Player left: %s" % self.get_user_name(id))
    
    def message(self, id, text):
        self.factory.to_irc("<%s> %s" % (self.get_user_name(id), text))
        
    def get_user_name(self, id):
        if id in self.users:
            return self.users[id]
        else:
            return "%s" % id
    
# Client factory
class ChatClientFactory(ReconnectingClientFactory):
        
    protocol = ChatServerClient
    
    def __init__(self, token, account_id):
        self.token = token
        self.account_id = account_id
        self.join_leave = True
        self.echoers = []
        
    def set_relay_bot(self, bot):
        self.relay_bot = bot
    
    def to_irc(self, message):
        self.relay_bot.to_irc(message)
        
    def to_lobby(self, message):
        for echoer in self.echoers:
            echoer.send_packet(PK_MESSAGE, [message])
        
    def show_joinleave(self):
        self.join_leave = True
        
    def hide_joinleave(self):
        self.join_leave = False    