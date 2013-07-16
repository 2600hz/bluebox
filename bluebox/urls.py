from django.conf.urls import patterns, include, url

urlpatterns = patterns('',
    url(r'^account/(?P<account_id>[a-f0-9]{32})/directory/', include('bluebox.directory.urls', namespace='directory')),
	url(r'^account/(?P<account_id)[a-f0-9]{32})/dialplan/', include('bluebox.dialplan.urls', namespace='dialplan'))
)