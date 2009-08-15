from lib.decorators import render_to

@render_to('server/list.html')
def list(request):
	return locals()