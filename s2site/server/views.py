from lib.decorators import render_to

from s2site.server.models import Server

@render_to('server/list.html')
def list(request):
	servers = Server.active.all()
	return locals()