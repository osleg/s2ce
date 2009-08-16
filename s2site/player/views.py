from lib.decorators import render_to

from s2site.player.forms import RegistrationForm

@render_to('player/home.html')
def home(request):
	return locals()

@render_to('player/profile.html')
def profile(request):
	return locals()
	
@render_to('player/register.html')
def register(request):
    if request.method == 'POST':
        form = RegistrationForm(data=request.POST)
        if form.is_valid():
            new_user = form.save()
            return HttpResponseRedirect(reverse('home'))
    else:
        form = RegistrationForm()
    
	return locals()