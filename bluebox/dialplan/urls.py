from django.conf.urls import patterns, include, url

urlpatterns = patterns('',
	url(r'^extensions/', include('bluebox.dialplan.extensions.urls', namespace='extensions'))
)