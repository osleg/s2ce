from sys import stdout
from struct import pack

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
    	print "received data"

    # Callbacks for indiviual packets
        
class ChatServerClient(ChatServer):
    
    def connectionMade(self):
       self.send_packet(PK_LOGIN, [self.factory.account_id, self.factory.token])
    
# Client factory
class ChatClientFactory(ReconnectingClientFactory):
        
    protocol = ChatServerClient
    
    def __init__(self, token, account_id):
        self.token = token
        self.account_id = account_id        